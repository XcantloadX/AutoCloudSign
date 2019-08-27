window.Icon = window.Icon || {};
var ICON_ELE = "<i class=\"{class}\"></i>";

function _new(className){
	return ICON_ELE.replace("{class}", className);
}

Icon = {
	info: _new("fa fa-info-circle"),
	success: _new("fa fa-check"),
	warn: _new("fa fa-exclamation-triangle"),
	error: _new("fa fa-times"),
};

Icon.add = function(name, value){
	Icon[name] = value;
}