.. _configuration:

Events
===

PSR-14 events available:

**PosterImageCropVariantEvent**
In case the available crop variants for Image reference have been changed in your installation,
this event can be used to change the used crop variant for the video poster image

PosterImageCropVariantEvent Listener
====================================

This example demonstrates how to register an event listener that modifies
the crop variant of a poster image.

Event Listener Class
--------------------

..  code-block:: php

    <?php
    declare(strict_types=1);

    namespace Vendor\MyExtension\EventListener;

    use TRAW\VideoVtt\Events\PosterImageCropVariantEvent;
    use TYPO3\CMS\Core\Attribute\AsEventListener;

    #[AsEventListener(identifier: 'my-extension/poster-crop-variant')]
    class PosterImageCropVariantEventListener
    {
        public function __invoke(PosterImageCropVariantEvent $event): void
        {
            $event->setCropVariant('desktop');
        }
    }

Explanation
-----------

The listener reacts to the ``PosterImageCropVariantEvent`` and overrides
the crop variant by calling:

..  code-block:: php

    $event->setCropVariant('desktop');

Registration
------------

The event listener is registered automatically via the PHP attribute:

..  code-block:: php

    #[AsEventListener(identifier: 'lin-template/poster-crop-variant')]

No additional configuration in ``Services.yaml`` is required.

Result
------

Whenever the event is dispatched, the crop variant will be set to
``desktop``.

