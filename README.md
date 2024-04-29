**Video VTT**

Extends TYPO3 Video functionality by adding player attributes (mute, loop, showcontrols, etc.)
and VTT functionality for local videos.

Documentation: https://docs.typo3.org/p/traw/video-vtt/master/en-us/

Installation:
`composer req traw/video-vtt`


## Known issues
- Deprecation: #102032 - AbstractFile::FILETYPE_* constants in `TCA/Overrides/sys_file_reference.php`
- Editors (Non-Admin) can't disable or remove vtt track files, see https://github.com/thomasrawiel/video-vtt/issues/3
- some of the control options only apply to self-hosted videos (not yt, not vimeo)


