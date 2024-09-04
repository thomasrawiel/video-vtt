.. _text-tracks:

Text tracks and time-based data
==========================

Switch to the module :guilabel:`File > Filelist`.

Select the video where you want to add the vtt file and edit the metadata of the file

Switch to the :guilabel:`Video` tab and add or upload one or more vtt files

.. figure:: ../Images/Tracks/Tracks.jpg
   :class: with-shadow
   :alt: Add VTT file(s) to local video
   :width: 300px

   Add VTT file(s) to local video

.. note::
   After adding a vtt file, save the metadata once to see the additional fields

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

.. figure:: ../Images/Tracks/VideoplayerSubtitles.png
   :class: with-shadow
   :alt: Frontend rendering in the default video player
   :width: 300px

   Example frontend rendering in the default video player
