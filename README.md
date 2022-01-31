## Course Renamer

A simple PHP application to rename offline courses and import into media players playable, [TV shows lookalike](https://jellyfin.org/docs/general/server/media/shows.html), folder structure.

I was looking for a way to play my offline courses on my media player ([Jellyfin](https://jellyfin.org/)) and watch on variety of my devices. However, the folder format of the courses were not really playable by the media player in a proper manner. So I created this script so that it can convert my existing courses folders into a format similar to TV shows folder structures, preferred by media players.

#### Usage

```bash
# Clone the repo
git clone https://github.com/arunbabucode/course-renamer.git

# Build the container
docker build -t arunbabucode/course-renamer:latest .

# Run the container
docker run -it \
-v /Users/Arun/Desktop/Courses:/app/media \
arunbabucode/course-renamer \
rename \
--location=/app/media/MyAwesomeCourse \
--clean \
--dryrun

# Options
--location The absolute location folder of the course. (Mapped by docker volume)
--clean Remove files other than .mp4 and .srt.
--dryrun Dry run without touching actual files.
-L Alias for `location`
-C Alias for `clean`
-D Alias for `dryrun`
-h help

Note: You need to mount the folders using the docker volume (-v)

PS: Always make a backup of your original folder structure and files. Use `dryrun` to stimulate 
the output.
```


Currently, it expects the course folder in the below structure.

```bash
- My course
---- 1. Introduction
-------- 1. First chapter.mp4
-------- 1. First chapter.srt
-------- 1. First chapter.pdf
---- 2. Second Folder
-------- 1. First chapter.mp4
-------- 1. First chapter.srt
-------- 2. Second chapter.mp4
-------- 2. Second chapter.srt
```

After running the command, it converts the folder into below format.

```bash
- My course
---- Season 01 - 1. Introduction
-------- Episode S01E01 - 1. First chapter.mp4
-------- Episode S01E01 - 1. First chapter.srt
---- Season 02 - 2. Second Folder
-------- Episode S02E01 - 1. First chapter.mp4
-------- Episode S02E01 - 1. First chapter.srt
-------- Episode S02E02 - 2. Second chapter.mp4
-------- Episode S02E02 - 2. Second chapter.srt
```
