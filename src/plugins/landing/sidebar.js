import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { ToggleControl } from '@wordpress/components';
import { withDispatch } from '@wordpress/data';

const Sidebar = ( props ) => {
	const [ isFullScreen, setIsFullScreen ] = useState( false );

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
	}, [] );

	return (
		<>
			<ToggleControl
				label={ __( 'Full Screen Admin', 'insta-admin-landing-page' ) }
				checked={ isFullScreen }
				onChange={ ( value ) => {
					setIsFullScreen( value );
					props.setMetaFieldValue( '_ialp_full_screen', value );
				} }
				help={ __( 'Make the admin panel full screen.', 'insta-admin-landing-page' ) }
			/>
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
