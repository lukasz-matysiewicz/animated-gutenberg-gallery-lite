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
        
        // Default both features to ON if not set
        const lightboxEnabled = attributes.aggLightbox !== false;
        const animationsEnabled = attributes.aggAnimations !== false;

        return wp.element.createElement(
            wp.element.Fragment,
            null,
            wp.element.createElement(BlockEdit, props),
            wp.element.createElement(
                InspectorControls,
                null,
                wp.element.createElement(
                    PanelBody,
                    { title: __('Gallery Effects', 'animated-gutenberg-gallery') },
                    wp.element.createElement(ToggleControl, {
                        label: __('Enable Lightbox', 'animated-gutenberg-gallery'),
                        checked: lightboxEnabled,
                        onChange: (value) => setAttributes({ aggLightbox: value })
                    }),
                    wp.element.createElement(ToggleControl, {
                        label: __('Enable Animations', 'animated-gutenberg-gallery'),
                        checked: animationsEnabled,
                        onChange: (value) => setAttributes({ aggAnimations: value })
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