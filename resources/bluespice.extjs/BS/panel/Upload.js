Ext.define ( 'BS.panel.Upload', {
	extend: 'Ext.form.Panel',
	requires: [
		'BS.store.BSApi', 'BS.form.CategoryBoxSelect',
		'BS.dialog.UploadWarnings'
	],
	fileUpload: true,
	layout: {
		type: 'vbox',
		align: 'stretch'
	},

	/* Component specific */
	defaultFileNamePrefix: '',
	defaultCategories: [],
	defaultDescription: '',
	implicitFileNamePrefix: '',
	implicitCategories: [],
	implicitDescription: '',

	initComponent: function(){

		this.fuFile = new Ext.form.field.File({
			fieldLabel: mw.message('bs-upload-uploadfilefieldlabel').plain(),
			buttonText: mw.message('bs-upload-uploadbuttontext').plain(),
			id: this.getId()+'-file',
			name: 'file',
			emptyText: mw.message('bs-upload-uploadfileemptytext').plain(),
			validator: this.validateFile,
			validateOnChange: true,
			clearOnSubmit: false,
			msgTarget: 'under'
		});

		this.fuFile.on( 'change', this.fuFileChange, this);

		this.tfFileName = new Ext.form.TextField({
			fieldLabel: mw.message('bs-upload-uploaddestfilelabel').plain(),
			id: this.getId()+'-filename',
			/*jshint ignore:start */
			maskRe: new RegExp( /[^\/\?\*\"\#\<\>\|\ö\ä\ü\Ö\Ä\Ü\á\à\â\é\è\ê\ú\ù\û\ó\ò\ô\Á\À\Â\É\È\Ê\Ú\Ù\Û\Ó\Ò\Ô\ß\\]/gmi )
			/*jshint ignore:end */
		});

		this.tfFileName.on( 'change', this.tfFileNameChange, this);

		this.taDescription = new Ext.form.field.TextArea({
			fieldLabel: mw.message('bs-upload-descfilelabel').plain(),
			id: this.getId()+'-description',
			value: '',
			submitValue: false
		});

		this.storeLicenses = new BS.store.BSApi({
			apiAction: 'bs-upload-license-store',
			fields: ['text', 'value', 'indent'],
			submitValue: false
		});

		this.cbLicenses = new Ext.form.ComboBox({
			fieldLabel: mw.message('bs-upload-license').plain(),
			mode: 'local',
			store: this.storeLicenses,
			valueField: 'value',
			displayField: 'text',
			tpl: new Ext.XTemplate(
				'<ul class="x-list-plain">',
				  '<tpl for=".">',
				    '<tpl if="this.hasValue(value) == false">',
				      '<li role="option" class="x-boundlist-item no-value">{text}</li>',
				    '</tpl>',
				    '<tpl if="this.hasValue(value)">',
				      '<li role="option" class="x-boundlist-item indent-{indent}">{text}</li>',
				    '</tpl>',
				  '</tpl>',
				'</ul>',
				{
					compiled: true,
					disableFormats: true,
					// member functions:
					hasValue: function(value) {
						return value !== '';
					}
				}
			)
		});

		this.cbxWatch = new Ext.form.field.Checkbox({
			boxLabel: mw.message('bs-upload-uploadwatchthislabel').plain(),
			id: this.getId()+'watch_page',
			name: 'watchlist',
			inputValue: 'watch'
		});

		this.cbxWarnings = new Ext.form.field.Checkbox({
			boxLabel: mw.message('bs-upload-uploadignorewarningslabel').plain(),
			id: this.getId()+'ignorewarnings',
			checked: true,
			name: 'ignorewarnings'
		});

		this.bsCategories = new BS.form.CategoryBoxSelect({
			id: this.getId()+'categories',
			fieldLabel: mw.message('bs-upload-categories').plain(),
			submitValue: false,
			showTreeTrigger: true
		});
		this.bsCategories.setValue( this.defaultCategories );

		this.fsDetails = new Ext.form.FieldSet({
			title: mw.message('bs-upload-details').plain(),
			collapsed: true,
			collapsible: true,
			anchor: '98%',
			defaults: {
				anchor: '100%',
				labelAlign: 'right'
			}
		});

		this.panelItems = [
			this.fuFile,
			this.tfFileName,
			this.fsDetails
		];

		var detailsItems = [
			this.bsCategories,
			this.taDescription,
			this.cbLicenses,
			this.cbxWarnings,
			this.cbxWatch
		];

		$(document).trigger( 'BSUploadPanelInitComponent', [ this, this.panelItems, detailsItems ] );

		this.fsDetails.add( detailsItems );

		this.items = this.panelItems;

		this.callParent(arguments);
	},


	fuFileChange: function( field, value, eOpts ) {
		//Remove path info
		value = value.replace( /^.*?([^\\\/:]*?\.[a-z0-9]+)$/img, "$1" );
		value = this.defaultFileNamePrefix + value;
		value = value.replace( /\s/g, "_" );
		if( mw.config.get( 'bsIsWindows' ) ) {
			//replace non-ASCII
			var matcher = /[öäüÖÄÜáàâéèêúùûóòôÁÀÂÉÈÊÚÙÛÓÒÔß]/g;
			var dictionary = {
				  "ä": "ae", "ö": "oe", "ü": "ue",
				  "Ä": "Ae", "Ö": "Oe", "Ü": "Ue",
				  "á": "a", "à": "a", "â": "a",
				  "é": "e", "è": "e", "ê": "e",
				  "ú": "u", "ù": "u", "û": "u",
				  "ó": "o", "ò": "o", "ô": "o",
				  "Á": "A", "À": "A", "Â": "A",
				  "É": "E", "È": "E", "Ê": "E",
				  "Ú": "U", "Ù": "U", "Û": "U",
				  "Ó": "O", "Ò": "O", "Ô": "O",
				  "ß": "ss"
				};
			var translator = function( match ) {
				  return dictionary[match] || match;
			};

			value = value.replace( matcher, translator );
			value = value.replace( /[^\u0000-\u007F]/gmi, '' ); //Replace remaining Non-ASCII
		}
		//apply value without 'C:\fakepath\' to file field as well
		field.setRawValue( value );

		this.tfFileName.setValue( value );
		this.tfFileName.fireEvent( 'change', this.tfFileName, value );

	},

	tfFileNameChange: function( sender, newValue, oldValue, eOpts ) {
		var api = new mw.Api();
		var me = this;
		api.get({
			action: 'query',
			format: 'json',
			titles: 'File:' + newValue,
			prop: 'imageinfo',
			iiprop: 'uploadwarning',
			indexpageids: ''
		}).done( function ( response ) {
			//file does not exist
			if( response.query.pageids[0] === "-1" ) {
				return null;
			}
			//replacement warning, notify user and if user ignores, set ignore warnings automatically
			bs.util.alert(
				me.getId()+'-existswarning',
				{
					titleMsg: 'bs-extjs-title-warning',
					text: response.query.pages[response.query.pageids[0]]
						.imageinfo[0]
						.html
				},
				{
					ok: function() {
						me.cbxWarnings.setValue( true );
					},
					scope: me
				}
			);
		});
	},

	checkFileSize: function( ExtCmpId ) {
		//No FileAPI? No love.
		if(typeof window.FileReader === 'undefined') return true;

		var allowedSize = mw.config.get('bsMaxUploadSize');
		if( allowedSize === null ) return true;

		var filesize = this.fuFile.fileInputEl.dom.files[0].size;
		if( filesize > allowedSize.php || filesize > allowedSize.mediawiki) {
			return false;
		}
		return true;
	},

	uploadFile: function( sessionKeyForReupload ) {
		var params = {
			filename: this.makeFileName(),
			watchlist: this.cbxWatch.getValue() ? 'watch' : 'nochange',
			ignorewarnings: this.cbxWarnings.getValue(),
			text: this.makeFilePageText()
		};

		var me = this;
		var api = new mw.Api();
		var promise = null;

		if( sessionKeyForReupload ) {
			params.action = 'upload';
			params.filekey = sessionKeyForReupload;
			promise = api.postWithEditToken( params );
		}
		else {
			promise = api.upload( this.fuFile.fileInputEl.dom, params );
		}

		promise.done( function() {
			me.onUploadSuccess.apply( me, arguments );
		} )
		.fail( function( errorcode, response ) {
			/* When sending "ignorewarnings" MW API still calls "fail" */
			if( response.upload.result === 'Success' ) {
				me.onUploadSuccess.apply( me, [ response ] );
			}
			else {
				me.onUploadFailure.apply( me, arguments );
			}
		} );

		//TODO: Better mask whole document?
		this.getEl().mask(
			mw.message( 'bs-upload-upload-waitmessage' ).plain(),
			Ext.baseCSSPrefix + 'mask-loading'
		);
	},

	onUploadSuccess: function( response ) {
		var me = this;
		var pageText = this.makeFilePageText();
		/*
		 * 'pageText' consists of chosen categories, chosen licence and provided
		 * description text. If it is not empty - which means the user has set
		 * at least one of those fields - we need to edit the file description
		 * page after a successful upload. Because otherwise such things as
		 * categories would not be persisted in case of a re-upload.
		 */
		if( pageText === '' ) {
			this.fireEvent( 'upload-complete', me, response.upload );
			this.resetDialog();
			return;
		}

		var filePageEditApi = new mw.Api();
		filePageEditApi.postWithEditToken({
			action: 'edit',
			title: 'File:' + response.upload.filename,
			text: pageText
		})
		.then( function() {
			me.fireEvent( 'upload-complete', me, response.upload );
			me.resetDialog();
		});

	},

	onUploadFailure: function( errorcode, response ) {
		var upload = response.upload;
		if( upload.error ) {
			return this.handleError( upload );
		}

		if( upload.warnings && !upload.imageinfo ) {
			return this.handleWarnings( upload );
		}

		bs.util.alert(
			this.getId() + '-error',
			{
				title: mw.message( 'bs-upload-error' ).plain(),
				text: mw.message( 'bs-upload-error-long' ).plain()
			},
			{
				ok: this.resetDialog,
				scope: this
			}
		);
	},

	//scope: "this" == fuFile
	validateFile: function( value ) {
		if( value === "" ) return true;
		var me = this.up( 'form' );
		var nameParts = value.split('.');
		var fileExtension = nameParts[nameParts.length-1].toLowerCase();
		var extensionFound = false;

		for ( var i = 0; i < me.allowedFileExtensions.length; i++ ) {
			if ( me.allowedFileExtensions[i].toLowerCase() === fileExtension.toLowerCase() ) {
				extensionFound = true;
				break;
			}
		}
		if( !extensionFound ){
			return mw.message('bs-upload-filetypenotsupported').plain();
		}

		if( me.checkFileSize() === false ) {
			return mw.message( 'largefileserver' ).plain();
		}

		return true;
	},

	makeFilePageText: function() {
		var desc = this.implicitDescription;
		desc += this.taDescription.getValue();

		var license = this.cbLicenses.getValue();
		if( license ) {
			desc += license + "\n";
		}

		var categories = this.bsCategories.getValue();
		categories = categories.concat( this.implicitCategories );
		var formattedNamespaces = mw.config.get('wgFormattedNamespaces');
		for( var i = 0; i < categories.length; i++ ) {
			var categoryLink = new bs.wikiText.Link({
				title: categories[i].ucFirst(),
				nsText: formattedNamespaces[bs.ns.NS_CATEGORY],
				link: false //TODO: fix this in "bs.wikiText.Link"
			});
			desc += "\n" + categoryLink.toString();
		}

		return desc;
	},

	handleError: function( upload ) {
		var text = upload.error.info;

		//Mw API only renders un-localized windows-nonascii-filename
		if( upload.error.invalidparameter === "filename" ) {
			if( upload.error.info === 'windows-nonascii-filename' ) {
				text = mw.message( 'windows-nonascii-filename' ).plain();
			}
		}

		bs.util.alert(
			this.getId()+'-error',
			{
				title: mw.message( 'bs-upload-error' ).plain(),
				text: text
			},
			{
				ok: this.resetDialog,
				scope: this
			}
		);
	},

	handleWarnings: function( upload ) {
		var warnDlg = new BS.dialog.UploadWarnings( {
			id: this.getId()+'-warning',
			apiUpload: upload
		});

		warnDlg.on( 'ok', function( sender, response ) {
			this.cbxWarnings.setValue( true );
			this.uploadFile( upload.filekey );
		}, this );
		warnDlg.on( 'cancel', function() {
			this.getEl().unmask();
		}, this );

		warnDlg.show();
	},

	resetDialog: function() {
		this.getEl().unmask();
		this.getForm().reset(); //workaround for resetting fuFile field
		this.fuFile.setRawValue('');
	},

	makeFileName: function() {
		var fileName =
			this.implicitFileNamePrefix +
			this.tfFileName.getValue();

		return fileName;
	}
});
