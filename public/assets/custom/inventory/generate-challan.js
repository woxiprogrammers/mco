$(document).ready(function () {
    // CreateMaterialRequest.init();
    var inventoryData = JSON.parse(localStorage.getItem('inventoryData'))
    console.log('...', inventoryData)
    if (inventoryData['material'].length > 0) {
        console.log('in if.....')
        var materials = ''
        inventoryData['material'].forEach(element => {
            console.log('element ...', element)
            // var row = $("<tr />")
            // $("<td />", {value: element.value})

            materials += `
                <tr>
                    <td>
                        <input type="checkbox" value="${element.id}">
                        <input type="hidden" name="material[]"value="${element.id}">
                    </td>
                    <td>
                        <span>${element.name}</span>
                    </td>
                    <td>
                        <input type="number" step="0.10" max="${element.availQuantity}" class="form-control" name="material_quantity[${element.id}]"/>
                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                </tr>
            `;


        });
        $("#materialRows").html(materials)
    }

    if (inventoryData['material'].length > 0) {

    }



});