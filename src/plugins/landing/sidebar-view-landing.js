import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

const SidebarViewLanding = () => {
	return (
		<>
			<Button
				variant="tertiary"
				className="instaadmin-view-landing-button"
				href={ instaAdminLandingPageSidebar.landingPageUrl }
				target="_blank"
				rel="noopener noreferrer"
			>
				{ __( 'View Landing Page', 'instaadmin' ) }
			</Button>
		</>
	);
};

export default SidebarViewLanding;
