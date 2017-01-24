/**
 * This function use boobox. If bootbox retuns false, value of target returns in default attribute value.
 * @param {object} target target for handle.
 * @param {string} defaultValueAttribute attribute for get default value.
 * @param {string} confirmationMessage confirmation message for bootbox.
 * @returns {boolean}
 * @package app\assets\assets
 * @version 1.0
 * @copyright (c) 2014-2015 KFOSoftware Team <kfosoftware@gmail.com>
 */
function confirmChangeBack(target, defaultValueAttribute, confirmationMessage, confirmCallback) {
    var target = $(target);
    if (typeof target.changeToDefault != 'undefined') {
        target.changeToDefault = undefined;
        return true;
    }

    var value = target.val();
    var oldValue = target.attr(defaultValueAttribute);
    if (value != oldValue) {
        bootbox.confirm(confirmationMessage, function (ask) {
            if (!ask) {
                target.changeToDefault = true;
                target.val(oldValue).change();
            } else {
                target.attr(defaultValueAttribute, value);
                if (typeof confirmCallback != 'undefined')
                {
                    confirmCallback();
                }
            }
        });
    }

    return true;
}