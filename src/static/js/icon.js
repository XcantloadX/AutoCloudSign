window.Icon = window.Icon || {};
const ICON_ELE = "<i class=\"{class}\"></i>";

var icons = {
	new: function(className){
		return ICON_ELE.replace("{class}", className);
	},
};

Icon = {
	info: icons.new("fa fa-info-circle"),
	success: icons.new("fa fa-check"),
	warn: icons.new("fa fa-exclamation-triangle"),
	error: icons.new("fa fa-times"),
};

Icon.add = function(name, value){
	Icon[name] = value;
}