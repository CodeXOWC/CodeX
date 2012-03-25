function updateProductPreview(form) {
    var target = document.getElementById('product_preview');
    var multiple_pricings = document.getElementById('multiple_pricings');

    //This needs to be done because there is a hidden element with the same name.
    var in_stock = document.getElementById('in_stock');

    var preview = '';

    //Decide what class to use for the product depending on whether or not it
    //is in stock.
    if (!in_stock.checked) {
        preview += '<div class="product not_in_stock">\n';
    }
    else {
        preview += '<div class="product">\n';
    }

    //If the product uses custom HTML, enter this part of the function.
    if (document.getElementById('use_custom').checked) {
        if (in_stock.checked) {
            preview = form.custom.value;
        }
        else {
            preview = 'Nothing displayed for custom HTML products that are\n\
                    not in stock.';
        }
    }
    //If the product does NOT use custom HTML, enter this part of the function.
    else {
        if (form.product_image.value) {
            preview += '<img class="product-image" src="'+form.product_image.value+'" />\n';
        }

        preview += '<div class="product_info">\n';

        if (form.product_name.value) {
            preview += '<div class="product_attribute">\n<span class="product_title">\n<span class="product-title">\n'+form.product_name.value+'\n</span>\n</span>\n</div>\n<hr />\n';
        }
        if (!in_stock.checked) {
            preview += '<b>NOT IN STOCK</b>\n';
        }
        if (form.product_category.value) {
            preview += '<div class="product_attribute">\n<b>Category:</b> \n'+form.product_category.value+'\n</div>\n';
        }
        if (form.product_price || form.elements["product_price[]"]) {
            if (multiple_pricings.checked) {
                var price = form.elements["product_price[]"];
                var description = form.elements["product_price_desc[]"];
                preview += '<div style="display: none;" class="product-price">'+price[0].value+'</div>\n';
                preview += '<div class="product_attribute"><select class="product-attr-selection">\n';
                for (var i = 0; i < price.length; i++) {
                    preview += '<option googlecart-set-product-price="'+price[i].value+'">'+description[i].value+' - '+price[i].value+'</option>\n';
                }
                preview += '</select></div>\n';
            }
            else {
                preview += '<div class="product_attribute">\n<b>Price:</b>\n <span class="product-price">\n'+form.product_price.value+'\n</span>\n</div>\n';
            }
        }
        if (form.product_shipping.value) {
            preview += '<div class="product_attribute">\n<b>Shipping:</b>\n <span class="product-shipping">\n'+form.product_shipping.value+'\n</span>\n</div>\n';
        }
        if (form.product_description.value) {
            var description = form.product_description.value.replace(/(\n)/g, '<br />\n');
            preview += '<div class="product_attribute">\n<b>Description:</b>\n <span class="product-attr-description">\n'+description+'\n</span>\n</div>\n';
        }
        if (form.product_extra.value) {
            var extra = form.product_extra.value.replace(/\n/g, '<br />\n');
            preview += '\n<div class="product_attribute">\n<b>Extra Info:</b> \n<span class="product-attr-extra">\n'+extra+'\n</span>\n</div>\n';
        }
        if (form.user_input.value) {
            var attrname = 'product-attr-' + form.user_input.value.toLowerCase().replace(/\ /g, '-');
            preview += '<div class="product_attribute">\n<b>' + form.user_input.value + ':</b> <input type="text" class="' + attrname + '" />\n';
        }
        if (form.custom_dropdown_name && form.elements["custom_dropdown_option[]"]) {
            var dropdown_name = 'product-attr-' + form.custom_dropdown_name.value.toLowerCase().replace(/\ /g, '-');
            preview += '<div class="product_attribute">\n<b>' + form.custom_dropdown_name.value + ':</b> \n\
                <select class="' + dropdown_name + '" />\n';
            var option = form.elements["custom_dropdown_option[]"];
            for (var i = 0; i < option.length; i++) {
                preview += '<option>'+option[i].value+'</option>\n';
            }
            preview += '</select></div>';

        }

        //Display the "Add to cart" button only if the product is in stock.
        if (in_stock.checked) {
            preview += '<div role="button" alt="Add to cart" tabindex="0" class="googlecart-add-button"></div>\n';
        }
        preview += '</div>\n'; //close the product info class div
    }

    preview += '</div>\n'; //close the product class div

    //Update the custom HTML form field if the product is in stock and the custom
    //HTML form field is not currently in focus.
    if (document.activeElement != form.custom && in_stock.checked &&
        !document.getElementById('use_custom').checked) {
        form.custom.value = preview;
    }

    //Update the product preview box.
    if (preview) {
        target.innerHTML = preview;
    }
    else {
        target.innerHTML = 'Start filling out the form options to see a product\n\
preview.';
    }
}

function toggleCheckbox(form, target) {
    var t = document.getElementById(target);
    var use_custom = document.getElementById('use_custom');
    var in_stock = document.getElementById('in_stock');
    if (use_custom.checked) {
        t.style.display = 'table-row';
    /*
        for (var i = 0; i < form.elements.length; i++) {
            if (form.elements[i] != use_custom && form.elements[i] != form.custom && form.elements[i] != in_stock) {
                form.elements[i].disabled = true;
                form.elements[i].style.color = '#aaaaaa';
            }
        }
        */
    }
    else {
        t.style.display = 'none';
    /*
        for (var i = 0; i < form.elements.length; i++) {
            if (form.elements[i] != use_custom && form.elements[i] != form.custom && form.elements[i] != in_stock) {
                form.elements[i].disabled = false;
                form.elements[i].style.color = '';
            }
        }
        */
    }
    updateProductPreview(form);
}

function displayShortcode(id) {
    prompt('Paste this shortcode into a blog post to display your product:',
        '[checkout product="'+id+'"]');
}

function toggleMultiplePricings(form) {
    var box = document.getElementById('multiple_pricings');
    var tr = document.getElementById('price');
    if (!box.checked) {
        tr.innerHTML =
        '<td>Price</td><td><input type="text" name="product_price" \n\
                id="product_price" onkeyup="updateProductPreview('+form+')" /></td>';
    }
    else {
        tr.innerHTML = '<td>Price Options</td>\n\
<td><div id="prices">Desc: <input type="text" name="product_price_desc[]" onkeyup="updateProductPreview('+form+')" /> Price: <input type="text" name="product_price[]" onkeyup="updateProductPreview('+form+')" /><br /></div>\n\
<br /><a href="javascript:addPriceOption(\''+form+'\');" class="button-primary">Add option</a><br /><br /></td>';
    }
}

function addPriceOption(form) {
    var elem = document.createElement('span');
    var text = 'Desc: <input type="text" name="product_price_desc[]" onkeyup="updateProductPreview('+form+')" /> Price: <input type="text" name="product_price[]" onkeyup="updateProductPreview('+form+')" /><br />';
    elem.innerHTML = text;
    document.getElementById('prices').appendChild(elem);
}

function is_string(input){
    return typeof(input)=='string';
}

function addDropdown(form) {
    document.getElementById('add_dropdown_button').setAttribute("href", "javascript:removeDropdown(\""+form+"\")");
    document.getElementById('add_dropdown_button').innerHTML = "Remove custom dropdown";
    var div = document.getElementById('custom_dropdown');
    var newDiv = document.createElement("div");
    newDiv.innerHTML = "Title<br /><input name='custom_dropdown_name' type='text' onkeyup='updateProductPreview("+form+")' /><br />\n\
        <br />Option list<br />\n\
        <div id='custom_dropdown_options'>\n\
        <input type='text' name='custom_dropdown_option[]' onkeyup='updateProductPreview("+form+")' /><br />\n\
        <input type='text' name='custom_dropdown_option[]' onkeyup='updateProductPreview("+form+")' /><br />\n\
        </div>\n\
        <br /><a class='button-primary' href='javascript:addDropdownOption(\""+form+"\")'>Add option</a><br /><br />";

    div.appendChild(newDiv);
}

function addDropdownOption(form) {
    var div = document.getElementById('custom_dropdown_options');
    var elem = document.createElement("span");
    elem.innerHTML = "<input type='text' name='custom_dropdown_option[]' onkeyup='updateProductPreview("+form+")' /><br />";
    div.appendChild(elem);
}

function removeDropdown(form) {
    var div = document.getElementById('custom_dropdown');
    div.innerHTML = "";
    document.getElementById('add_dropdown_button').setAttribute("href", "javascript:addDropdown(\""+form+"\")");
    document.getElementById('add_dropdown_button').innerHTML = "Add custom dropdown";
}

jQuery(document).ready(function(){
    jQuery('.lbakgc_help[title]').tooltip({
        'position': 'center right',
        'tipClass': 'lbakgc_tooltip'
    });
})