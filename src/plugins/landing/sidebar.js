import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { ToggleControl, PanelBody, PanelRow, TextControl } from '@wordpress/components';
import { withDispatch, select, subscribe } from '@wordpress/data';
import { cleanForSlug } from '@wordpress/url';

const Sidebar = ( props ) => {
	const [ isFullScreen, setIsFullScreen ] = useState( false );
	const [ adminSlug, setAdminSlug ] = useState( 'insta-admin' );
	const [ adminMenuTitle, setAdminMenuTitle ] = useState( __( 'Site Features', 'insta-admin-landing-page' ) );

	/* Subscribe to post updates and update the slug */
	subscribe( () => {
		const currentPostId = select( 'core/editor' ).getCurrentPostId();
		const currentPost = select( 'core' ).getEntityRecord( 'postType', 'insta_admin_landing', currentPostId );
		const isSaving = select( 'core/editor' ).isSavingPost();
		// Update slug in the text control to match the saved slug in meta.
		if ( isSaving && currentPost && currentPost.status === 'private' && currentPost.modified_gmt !== currentPost.date_gmt ) {
			const meta = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' );
			if ( typeof ( meta ) === 'undefined' ) {
				return;
			}

			// Find and sanitize slug.
			if ( meta._ialp_slug !== null && typeof ( meta._ialp_slug ) !== 'undefined' ) {
				setAdminSlug( cleanForSlug( meta._ialp_slug ) );
			}
		}
	} );

	/* Initialize the initial state */
	useEffect( () => {
		const meta = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' );
		if ( typeof ( meta ) === 'undefined' ) {
			props.setMetaFieldValue( '_ialp_full_screen', false );
			setIsFullScreen( false );
			return;
		}
		// Check for meta key _ialp_full_screen
		if ( meta._ialp_full_screen === null || typeof ( meta._ialp_full_screen ) === 'undefined' ) {
			props.setMetaFieldValue( '_ialp_full_screen', false );
			setIsFullScreen( false );
		} else {
			setIsFullScreen( meta._ialp_full_screen );
		}

		// Set admin slug.
		if ( meta._ialp_slug === null || typeof ( meta._ialp_slug ) === 'undefined' ) {
			props.setMetaFieldValue( '_ialp_slug', 'insta-admin' );
			setAdminSlug( 'insta-admin' );
		} else {
			setAdminSlug( meta._ialp_slug );
		}

		// Set admin title.
		if ( meta._ialp_menu_title === null || typeof ( meta._ialp_menu_title ) === 'undefined' ) {
			props.setMetaFieldValue( '_ialp_menu_title', __( 'Site Features', 'insta-admin-landing-page' ) );
			setAdminMenuTitle( __( 'Site Features', 'insta-admin-landing-page' ) );
		} else {
			setAdminMenuTitle( meta._ialp_menu_title );
		}
	}, [] );

	return (
		<>
			<PanelBody initialOpen={ true } title={ __( 'Appearance', 'quotes-dlx' ) }>
				<PanelRow>
					<ToggleControl
						label={ __( 'Full Screen Admin', 'insta-admin-landing-page' ) }
						checked={ isFullScreen }
						onChange={ ( value ) => {
							setIsFullScreen( value );
							props.setMetaFieldValue( '_ialp_full_screen', value );
						} }
						help={ __( 'Make the admin panel full screen.', 'insta-admin-landing-page' ) }
					/>
				</PanelRow>
			</PanelBody>
			<PanelBody initialOpen={ true } title={ __( 'Settings', 'quotes-dlx' ) }>
				<PanelRow>
					<TextControl
						label={ __( 'Landing Page Slug', 'insta-admin-landing-page' ) }
						value={ adminSlug }
						onChange={ ( value ) => {
							setAdminSlug( value );
							props.setMetaFieldValue( '_ialp_slug', cleanForSlug( value ) );
						} }
						help={ __( 'Set the slug for the landing page.', 'insta-admin-landing-page' ) }
					/>
				</PanelRow>
				<PanelRow>
					<TextControl
						label={ __( 'Menu Title', 'insta-admin-landing-page' ) }
						value={ adminMenuTitle }
						onChange={ ( value ) => {
							setAdminMenuTitle( value );
							props.setMetaFieldValue( '_ialp_menu_title', value );
						} }
						help={ __( 'Set the menu title used in the admin sidebar.', 'insta-admin-landing-page' ) }
					/>
				</PanelRow>
			</PanelBody>
		</>
	);
};

export default withDispatch( ( dispatch ) => {
	return {
		setMetaFieldValue( key, value ) {
			dispatch( 'core/editor' ).editPost( { meta: { [ key ]: value } } );
		},
	};
} )( Sidebar );
