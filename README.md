# Remove Scaled Images

- When you upload large images in WordPress, it creates scaled version with a max width.
- But it keeps the original. This uses disk spaces. Lots of disk space if the original files where huge.
- The original file is (`original.jpeg`) and the scaled version (`original-scaled.jpeg`) are both kept in `uploads`.

This utility will find all `-scaled` images and copy them over the original - essentially keeping the smaller version as the original.

I have seen savings of 60% of the total disk space used.

An alternative is to replace the original with scaled version, but often the scaled version is the one referenced in posts. This would require a database update to refererence the image tags, etc. I'll avoid adhoc database updates and settle for slightly more disk usage.

## Installation

- zip the `remove-scaled-images` folder

```
zip -r remove-scaled-images.zip remove-scaled-images
```

- In WordPress, install as a plugin
