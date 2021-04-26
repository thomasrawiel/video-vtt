.. include:: ../Includes.txt

.. _for-editors:

===========
For Editors
===========

.. _adding-vtt-files:

Adding VTT files to videos
==========================

In the filelist edit the metadata of a local video file.
Add one or more vtt file and choose the appropriate track type

.. figure:: ../Images/Screenshots/Videofilemetadata.png
   :class: with-shadow
   :alt: Add VTT file(s) to local video
   :width: 300px

   Add VTT file(s) to local video

.. figure:: ../Images/Screenshots/VttAttributes.png
   :class: with-shadow
   :alt: Metadata Attributes added by EXT:video_vtt
   :width: 300px

   Metadata Attributes added by EXT:video_vtt


**Track label**
A user-readable title of the text track which is used by the browser when listing available text tracks.

**Track language**
Language of the track text data. It must be a valid BCP 47 language tag. (e.g. de, en, etc.)

**Track type**
How the text track is meant to be used. (subtitles, captions, chapters, etc.)

**Default attribute**
When active, adds the 'default' attribute to the rendered track.

.. tip::

   Adding the default track is useful, for example if you wish to enable subtitle by default.

.. important::

   When adding multiple tracks, keep in mind that the default attribute should be set only for one track.

.. _video-controls:

Video Controls
==============

For YouTube, Vimeo and local videos you can set certain attributes which are added to the video tag in the frontend.

.. figure:: ../Images/Screenshots/VideoSwitches.png
   :class: with-shadow
   :alt: Switches in Video Metadata
   :width: 300px

   Video control switches in Video Metadata


.. tip::

   On mobile devices, videos typically need to be muted when autoplay is activated.
