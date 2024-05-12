/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */
jQuery((function(t){t(document).ready((function(){var e="";(e=t("#jform_tagids_chosen").length?t("#jform_tagids_chosen"):t("#jform_tagids_chzn")).find("input").keyup((function(i){if(this.value&&this.value.length>=3&&(13===i.which||188===i.which)){var r=e.find("li.active-result.highlighted").first();if(13===i.which&&""!==r.text()){var n="#new#"+r.text();t("#jform_tagids option").filter((function(){return t(this).val()==n})).remove(),(s=t("#jform_tagids option").filter((function(){return t(this).html()==r.text()}))).attr("selected","selected")}else{var s,a=this.value;if(""!==(s=t("#jform_tagids option").filter((function(){return t(this).html()==a}))).text())s.attr("selected","selected");else{var l=t("<option>");l.text(this.value).val("#new#"+this.value),l.attr("selected","selected"),t("#jform_tagids").append(l)}}this.value="",t("#jform_tagids").trigger("liszt:updated"),i.preventDefault()}}))}))}));