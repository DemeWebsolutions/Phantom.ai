/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * Register: Example Block
 *
 * Created with Phantom.ai workflow automation
 */
registerBlockType( 'phantom-ai/example-block', {
	/**
	 * Edit function
	 *
	 * @param {Object} props Block properties
	 * @return {WPElement} Element to render
	 */
	edit: ( props ) => {
		const { attributes, setAttributes } = props;
		const blockProps = useBlockProps();

		return (
			<div { ...blockProps }>
				<RichText
					tagName="p"
					value={ attributes.content }
					onChange={ ( content ) => setAttributes( { content } ) }
					placeholder="Enter your content..."
				/>
			</div>
		);
	},

	/**
	 * Save function
	 *
	 * @param {Object} props Block properties
	 * @return {WPElement} Element to render
	 */
	save: ( props ) => {
		const { attributes } = props;
		const blockProps = useBlockProps.save();

		return (
			<div { ...blockProps }>
				<RichText.Content
					tagName="p"
					value={ attributes.content }
				/>
			</div>
		);
	},
} );
