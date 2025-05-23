const AclPermissionsExtended = {
  initializeEvents () {
		document.getElementById('acl-submit-button-1748022412705').onclick = function() {
			document.aclfilterform.action = document.location;
			document.aclfilterform.submit();
		}
  }
}

AclPermissionsExtended.initializeEvents()
