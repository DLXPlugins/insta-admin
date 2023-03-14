import Sidebar from './sidebar';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
registerPlugin( 'insta-admin-landing-page-options', {
	icon: (
		<svg
			viewBox="0 0 2134 2134"
			xmlns="http://www.w3.org/2000/svg"
			xmlSpace="preserve"
			width={ 24 }
			height={ 24 }
		>
			<path
				d="M1655.89 830.429c-12.267-19.066-33.334-30.4-55.867-30.4h-466.667V66.696c0-31.467-22-58.667-52.8-65.2-31.333-6.667-62 9.466-74.8 38.133l-533.333 1200c-9.2 20.534-7.2 44.534 5.066 63.333 12.267 18.934 33.334 30.4 55.867 30.4h466.667v733.334c0 31.466 22 58.666 52.8 65.2 4.666.933 9.333 1.467 13.866 1.467 25.867 0 50-15.067 60.934-39.601l533.333-1200c9.067-20.667 7.333-44.401-5.066-63.334Z"
				style={ {
					fill: '#ffc107',
					fillRule: 'nonzero',
				} }
			/>
		</svg>
	),
	render: () => {
		return (
			<>
				<PluginSidebarMoreMenuItem target="insta-admin-landing-sidebar">
					{ __( 'InstaAdmin Options', 'insta-admin-landing-page' ) }
				</PluginSidebarMoreMenuItem>
				<PluginSidebar
					name="insta-admin-landing-sidebar"
					title={ __( 'InstaAdmin Options', 'insta-admin-landing-page' ) }
				>
					<Sidebar />
				</PluginSidebar>
			</>
		);
	},
} );
