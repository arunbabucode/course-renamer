<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use \Laminas\Stdlib\Glob;
use function count;
use function glob;
use function in_array;
use function pathinfo;
use function preg_match;
use function rename;
use function sprintf;
use function unlink;

class RenamerCommand extends Command
{
    const FILE_EXTENSION_MP4 = 'mp4';
    const FILE_EXTENSION_SRT = 'srt';

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'rename
                            {--L|location= : The course folder absolute location}
                            {--D|dryrun : Dry run mode to stimulate the output}
                            {--C|clean : Remove unwanted files not supported by media player}
                            ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Rename courses for Media players';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $location = $this->option('location');
        $dryRun = $this->option('dryrun');
        $clean = $this->option('clean');

        if (empty($location)) {
            $this->error('Invalid location');
            $this->call('help', ['command_name' => 'rename', 'format' => 'raw']);
            return;
        }

        $directories = Glob::glob($location . '/*', Glob::GLOB_ONLYDIR);
        if (!$dryRun) {
            $bar = $this->output->createProgressBar(count($directories));
            $bar->start();
        }
        foreach ($directories as $directory) {

            $folderPathInfo = pathinfo($directory);
            $seasonNumber = (int)$folderPathInfo['filename'];

            $seasonName = sprintf('Season %02d - %s', $seasonNumber, $folderPathInfo['basename']);
            $newFolderLocation = $folderPathInfo['dirname'] . '/' . $seasonName;

            //only apply on videos, subtitles
            $files = Glob::glob($directory . '/*.{mp4,srt}', Glob::GLOB_BRACE);

            //remove unsupported files from media player.
            $removableFiles = [];
            if ($clean) {
                $cleanableFiles = Glob::glob($directory . '/*', Glob::GLOB_BRACE);
                foreach ($cleanableFiles as $cleanableFile) {
                    $whiteListedExtensions = [self::FILE_EXTENSION_MP4, self::FILE_EXTENSION_SRT];
                    $extension = pathinfo($cleanableFile)['extension'];

                    if (!in_array($extension, $whiteListedExtensions)) {

                        $removableFiles[] = [
                            'newFile' => $cleanableFile,
                        ];

                        if (!$dryRun) {
                            unlink($cleanableFile);
                        }
                    }
                }

                if ($dryRun) {
                    $this->error('Removable files.');
                    $this->table(
                        [$newFolderLocation],
                        $removableFiles
                    );
                }
            }

            //rename files first
            $outputDryRun = [];
            foreach ($files as $file) {
                $filePathInfo = pathinfo($file);
                $fileName = $filePathInfo['filename'];
                if (!preg_match('/(Episode)+\sS[0-9]+E[0-9]+/i', $fileName)) {
                    preg_match('/([0-9]+)\.+/', $fileName, $matches);
                    $episodeNumber = (int)($matches[1]);

                    $episodeName = sprintf('Episode S%02dE%02d - %s', $seasonNumber, $episodeNumber, $filePathInfo['basename']);
                    $newFileLocation = $filePathInfo['dirname'] . '/' . $episodeName;
                    $outputDryRun[] = [
                        'newFile' => $newFileLocation,
                    ];

                    if (!$dryRun) {
                        rename($file, $newFileLocation);
                    }
                }
            }

            if (!preg_match('/(Season)+\s[0-9]+\s-/i', $directory)) {
                if ($dryRun) {
                    $this->info('Output files sample.');
                    $this->table(
                        [$newFolderLocation],
                        $outputDryRun
                    );
                }
                //rename folder
                if (!$dryRun) {
                    rename($directory, $newFolderLocation);
                    $bar->advance();
                }
            }
        }
        if (!$dryRun) {
            $bar->finish();
            $this->newLine();
            $this->info('The operation is successful.');
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
