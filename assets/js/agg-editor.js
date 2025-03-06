const { createHigherOrderComponent } = wp.compose;
const { InspectorControls } = wp.blockEditor || wp.editor;
const { PanelBody, ToggleControl } = wp.components;
const { __ } = wp.i18n;

const withGalleryControls = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        if (props.name !== 'core/gallery') {
            return wp.element.createElement(BlockEdit, props);
        }

        const { attributes, setAttributes } = props;
        
        // Only lightbox toggle in lite version
        const lightboxEnabled = attributes.aggLightbox !== false;

        return wp.element.createElement(
            wp.element.Fragment,
            null,
            wp.element.createElement(BlockEdit, props),
            wp.element.createElement(
                InspectorControls,
                null,
                wp.element.createElement(
                    PanelBody,
                    { title: __('Gallery Effects', 'animated-g-gallery-lite') },
                    wp.element.createElement(ToggleControl, {
                        label: __('Enable Lightbox', 'animated-g-gallery-lite'),
                        checked: lightboxEnabled,
                        onChange: (value) => setAttributes({ aggLightbox: value })
                    })
                )
            )
        );
    };
}, 'withGalleryControls');

wp.hooks.addFilter(
    'editor.BlockEdit',
    'agg/gallery-controls',
    withGalleryControls
);