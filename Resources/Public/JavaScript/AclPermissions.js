/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Module: Tx/BeAcl/AclPermissions
 * Javascript functions regarding the permissions acl module
 */
import $ from 'jquery';
import Notification from '@typo3/backend/notification.js';

const ajaxUrl = TYPO3.settings.ajaxUrls['user_access_permissions'];

const AclPermissions = {
	options: {
		containerSelector: '#PermissionControllerEdit'
	},
	newACLs: [],
	currentACLs: [],
	editAclRowTpl: null,
	
	getEditAclRowTpl() {
		if (!this.editAclRowTpl) {
			this.editAclRowTpl = $('#tx_beacl-edit-acl-row-template').html();
		}
		return this.editAclRowTpl;
	},
	
	/**
	 * generates new hidden field
	 *
	 * @param {string} name - name of field
	 * @param {string} value - value of field
	 */
	createNewHiddenField(name, value) {
		const hiddenFields = document.getElementById('insertHiddenFields');
		const hiddenStore = document.createElement('input');
		hiddenStore.setAttribute('type', 'hidden');
		hiddenStore.setAttribute('value', value);
		hiddenStore.setAttribute('name', name);
		hiddenFields.appendChild(hiddenStore);
	},
	
	/**
	 * create new ACL ID
	 */
	getNewId() {
		return 'NEW' + Math.round(Math.random() * 10000000);
	},
	
	/**
	 * add ACL
	 */
	addACL() {
		const $container = $(this.options.containerSelector);
		const pageID = $container.data('pageid');
		const ACLid = this.getNewId();
		// save ACL ID in the new ACLs array
		this.newACLs.push(ACLid);
		// Create table row
		const tableRow = this.getEditAclRowTpl().replace(/###uid###/g, ACLid);
		// append line to table
		$('#typo3-permissionMatrix tbody').append(tableRow);
	},
	
	removeACL(id) {
		const $tableRow = $('#typo3-permissionMatrix tbody').find(`tr[data-acluid="${id}"]`);
		if ($tableRow.length) $tableRow.remove();
	},
	
	/**
	 * Group-related: Set the new group by executing an ajax call
	 *
	 * @param {Object} $element
	 */
	deleteACL($element) {
		const $container = $(this.options.containerSelector);
		const pageID = $container.data('pageid');
		const id = $element.data('acluid');
		
		if (isNaN(id)) {
			this.removeACL(id);
			return;
		}
		
		$.ajax({
			url: ajaxUrl,
			type: 'post',
			dataType: 'html',
			cache: false,
			data: {
				'action': 'delete_acl',
				'page': pageID,
				'acl': id
			}
		}).done(data => {
			this.removeACL(id);
			const title = data.title || 'Success';
			const msg = data.message || 'ACL deleted';
			Notification.success(title, msg, 5);
		}).fail((jqXHR, textStatus, error) => {
			Notification.error(null, error);
		});
	},
	
	/**
	 * update user and group information
	 *
	 * @param {string} ACLid - ID of ACL
	 * @param {number} typeVal - type value
	 * @param {number} objectId - Selected object id
	 */
	updateUserGroup(ACLid, typeVal, objectId = 0) {
		const $container = $(this.options.containerSelector);
		const pageID = $container.data('pageid');
		const type = typeVal === 1 ? 'group' : 'user';
		
		// get child nodes of user/group selector
		const $selector = $(`select[name="data[pages][${pageID}][perms_${type}id]"]`);
		// delete current object selector options
		const $objSelector = $(`select[name=data\\[tx_beacl_acl\\]\\[${ACLid}\\]\\[object_id\\]]`);
		$objSelector.empty();
		
		// set new options on object selector
		$selector.children().each(function () {
			const $option = $(this);
			const $clonedOption = $option.clone();
			$objSelector.append($clonedOption);
		});
	},
	
	/**
	 * initializes events using deferred bound to document
	 * so AJAX reloads are no problem
	 */
	initializeEvents() {
		const _this = this;
		$(this.options.containerSelector)
			.on('change', '.tx_beacl-edit-type-selector', function(evt) {
				evt.preventDefault();
				const $el = $(evt.target);
				_this.updateUserGroup($el.data('acluid'), $el.val(),0);
			})
			.on('click', '.tx_beacl-addacl', function(evt) {
				evt.preventDefault();
				_this.addACL();
			})
			.on('click', '.tx_beacl-edit-delete', function(evt) {
				evt.preventDefault();
				_this.deleteACL($(this));
			})
			.find('.tx_beacl-edit-acl-row').each(function() {
				const acluid = $(this).data('acluid');
				if (acluid) {
					$(this).find('[data-checkbox-group]');
					_this.currentACLs.push(acluid);
				}
			});
	}
};

AclPermissions.initializeEvents()
