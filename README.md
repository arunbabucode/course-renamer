## Course Renamer

A simple PHP application to rename offline courses and import into media players playable, [TV shows lookalike](https://jellyfin.org/docs/general/server/media/shows.html), folder structure.

I was looking for a way to play my offline courses on my media player ([Jellyfin](https://jellyfin.org/)) and watch on variety of my devices. However, the folder format of the courses were not really playable by the media player in a proper manner. So I created this script so that it can convert my existing courses folders into a format similar to TV shows folder structures, preferred by media players.

#### Usage

```bash
# Clone repo

# Build the container
docker build -t arunbabucode/course-renamer:latest .

# Run them
docker run -it \
-v /Users/Arun/Desktop/Courses:/app/media \
arunbabucode/course-renamer \
rename \
--location=/app/media/renamer \
--clean \
--dryrun

# Options
-h help
--location The absolute location mapped by the volume.
--clean Remove files other than .mp4 and .srt.
--dryrun Dry run without touching actual files.
-L Alias for `location`
-C Alias for `clean`
-D Alias for `dryrun`

Note: You need to mount the folders using the docker volume (-v)
 
```


Currently works with the course folder structure as below.

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
