import Sidebar from './sidebar';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

registerPlugin( 'insta-admin-landing-page-options', {
	icon: (
		<svg
			viewBox="0 0 64 64"
			width={ 24 }
			height={ 24 }
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
		>
			<path
				d="M32.868 4c2.215 0 4.43.23 6.645.918a14.78 14.78 0 0 1 5.13 2.754c1.515 1.262 2.565 2.869 3.38 4.705.817 2.18 1.283 4.36 1.167 6.656 0 2.18-.467 4.246-1.516 6.197-.933 1.72-2.332 3.213-4.197 4.13 2.565.919 4.78 2.755 6.296 4.935 1.632 2.64 2.331 5.623 2.215 8.721 0 5.509-1.633 9.755-5.014 12.623C43.594 58.51 39.047 60 33.218 60H12V4h20.868Zm-10.26 9.525v12.164h9.677c1.749.114 3.498-.345 4.897-1.378 1.165-.918 1.748-2.524 1.748-4.82 0-2.294-.583-3.9-1.748-4.819-1.516-1.033-3.381-1.492-5.13-1.377l-9.443.23Zm0 21.803V50.36h9.677c3.265 0 5.713-.689 7.112-1.951 1.515-1.492 2.215-3.557 2.098-5.623.117-2.066-.583-4.131-2.098-5.623-1.4-1.262-3.73-1.95-7.112-1.95l-9.676.114Z"
				fill="#3243B2"
			/>
		</svg>
	),
	render: () => {
		return (
			<>
				<PluginDocumentSettingPanel title={ __( 'Admin Page Options', 'insta-admin-landing-page' ) }>
					<Sidebar />
				</PluginDocumentSettingPanel>
			</>
		);
	},
} );
