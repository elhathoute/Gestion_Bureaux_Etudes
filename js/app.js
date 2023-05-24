$(document).ready(function () {
      // approve all notifications
   
    
    /**
     * changing required property for add client inputs
     */
    // $(document).on('click','#indiv-tab',function(){
    //     $("#indivRadioBtn").click();
    //     $('#indiv-tabs input').prop('required',true);
    //     $('#entrep-tabs input').prop('required',false);
        
    // });
    // $(document).on('click','#entrep-tab',function(){
    //     $("#entrepRadioBtn").click();
    //     $('#entrep-tabs input').prop('required',true);
    //     $('#indiv-tabs input').prop('required',false);
    // });
//-------------------------------------------------------------------------------
//to display the broker select in create client 
        // $('#brokerCheckBox').change(function() {
        // if ($(this).is(':checked')) {
        //     $('#broker_div').show();
        // } else {
        //     $('#broker_div').hide();
        // }
        // });
//-------------------------------------------------------------------------------
    // var el = [];
    $(".hide-element").each(function(node){
        // el.push($(this));
        $(this).remove();
    });
    // console.log(el);

    /**
     * fetching data to individual customer table
     */

    $('#myTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
        'createdRow':function(row,data,dataIndex){
                $(row).attr('id',data[0]);
        },
        
    });


    // $('#myTable').DataTable({
    //     'info':false,
    //     'paging':true,
    //     'responsive':true,
    //     'serverSide':true,
    //     'processing':true,
    //     'autoWidth':true,
    //     // 'order':[],
    //     'ajax':{
    //         'url':'fetch_indv_customer_data.php',
    //         'type':'POST',
    //     },
    //     'createdRow':function(row,data,dataIndex){
    //         $(row).attr('id',data[0]);
    //     },
    //     'columnDefs':[{
    //         'target':[0,6],
    //         'orderable':true,
    //     }]
    // });

// fill the broker model with data 

    $(document).on('click','.editBtn',function(){
        var id = $(this).data('id');
        // console.log(id);
        var tr_id = $(this).closest('tr').attr('id');
        // console.log(tr_id);
        $.ajax({
            url:'get_selected_cus.php',
            data:{id:id},
            type:"post",
            success:function(data){
                var json = JSON.parse(data);
                var customer = json.customer;
                var brokers = json.brokers;
                $("#id").val(customer.id);
                $("#tr_id").val(tr_id);
                $("#prenom").val(customer.prenom);
                $("#nom").val(customer.nom);
                $("#email").val(customer.email);
                $("#phone").val(customer.tel);
                $("#address").val(customer.address);


                // Populate broker options
                var brokerSelect = $("#broker");
                // Clear previous options
                brokerSelect.empty(); 

                // Add options to the select element
                var option1 = $('<option>Sélectionnez un intermédiaire</option>');
                brokerSelect.append(option1);
                brokers.forEach(function(broker) {
                    var option = $('<option></option>').attr('value', broker.phone).text(broker.nom);
                    brokerSelect.append(option);
                });
                brokerSelect.on('change', function() {
                    var selectedPhone = $(this).val();
                    $("#phone").val(selectedPhone);
                });
            }
        });
        
    });
    

    /**
     * update button click for customer Form
     */
    
    $(document).on('submit','#editCusForm',function(){
        
        var id = $("#id").val();
        var tr_id = $("#tr_id").val();
        var firstName = $("#prenom").val();
        var lastName = $("#nom").val();
        var email = $("#email").val();
        var phone = $("#phone").val();
        var address = $("#address").val();
        
        $.ajax({
            url:"customer-edit.php",
            data:{id:id,firstName:firstName,lastName:lastName,email:email,phone:phone,address:address},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    var table = $("#myTable").DataTable();
                    var button = '<a href="javascript:void(0);" data-id="'+id+'" class="btn btn-primary btn-sm editBtn" data-bs-toggle="modal" data-bs-target="#EditCusModal" ><span><i class="bi bi-pencil-square"></i></span></a> <a href = "javascript:void(0);" data-id="'+id+'" class=" btn btn-danger btn-sm deleteBtn"><span><i class="bi bi-trash"></i></span></a>';
                    var row = table.row("[id='"+tr_id+"']");
                    row.row("[id='"+tr_id+"']").data([tr_id,firstName,lastName,email,phone,address,button]);
                    // cleaning inputs
                    $("#prenom").val('');
                    $("#nom").val('');
                    $("#email").val('');
                    $("#phone").val('');
                    $("#address").val('');
                    $("#EditCusModal").modal('hide');
                }else{
                    alert('Failed: connecting with Database error');
                }
            },
        });
    });

    /**
     * delete button click for customer Form
     */
    var indv_deleted_id,indv_deleted_row_id;
    $(document).on('click','.deleteBtn',function(event){
        $("#deleteModal").modal('show');
        indv_deleted_id = $(this).data('id');
        indv_deleted_row_id = $(this).parent().closest('tr').attr("id");
        // alert(indv_deleted_id);
    });
    
    $(document).on('click','.deleteModalBtn',function(){
        // console.log(indv_deleted_id);
        $.ajax({
            url:'customer-delete.php',
            data:{id:indv_deleted_id},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $('#myTable #'+indv_deleted_row_id).closest('tr').remove();
                    $("#deleteModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });
    /**
     * END fetching data to individual customer table
     */

    
    /**
     * fetching data to Entreprise customer table
     */

    $('#entrepTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
        'createdRow':function(row,data,dataIndex){
                $(row).attr('id',data[0]);
        },
    });

    // $('#entrepTable').DataTable({
    //     'info':false,
    //     'paging':true,
    //     'responsive':true,
    //     'serverSide':true,
    //     'processing':true,
    //     'autoWidth':true,
    //     // 'order':[],
    //     'ajax':{
    //         'url':'fetch_entrep_cus_data.php',
    //         'type':'POST',
    //     },
    //     'createdRow':function(row,data,dataIndex){
    //         $(row).attr('id',data[0]);
    //     },
    //     'columnDefs':[{
    //         'target':[0,6],
    //         'orderable':true,
    //     }]
    // });
//fill the entreprise model with data 
    $(document).on('click','.editEntrepBtn',function(){
        var id = $(this).data('id');
        // console.log(id);
        var tr_id = $(this).closest('tr').attr('id');
        // console.log(tr_id);
        $.ajax({
            url:'get_selected_entrepCus.php',
            data:{id:id},
            type:"post",
            success:function(data){
                var json = JSON.parse(data);
                var customer = json.customer;
                var brokers = json.brokers;
                $("#id_ent").val(customer.id);
                $("#tr_id_ent").val(tr_id);
                $("#nom_ent").val(customer.nom);
                $("#ice").val(customer.ICE);
                $("#email_ent").val(customer.email);
                $("#phone_ent").val(customer.tel);
                $("#address_ent").val(customer.address);
                $("#EditCusEntrepModal").modal('show');
                

                // Populate broker options
                var brokerSelect = $("#brokerEntr");
                // Clear previous options
                brokerSelect.empty(); 

                // Add options to the select element
                var option1 = $('<option>Sélectionnez un intermédiaire</option>');
                brokerSelect.append(option1);
                brokers.forEach(function(broker) {
                    var option = $('<option></option>').attr('value', broker.phone).text(broker.nom);
                    brokerSelect.append(option);
                });
                brokerSelect.on('change', function() {
                    var selectedPhone = $(this).val();
                    // alert(selectedPhone);
                    $(".entrePhone").val(selectedPhone);
                });
                
            }
        });
        
    });

    /**
     * update button click for customer Entreprise Form
     */
    
    $(document).on('submit','#editCusEntrepForm',function(){
        
        var id = $("#id_ent").val();
        var tr_id = $("#tr_id_ent").val();
        var name = $("#nom_ent").val();
        var ice = $("#ice").val();
        var email = $("#email_ent").val();
        var phone = $("#phone_ent").val();
        var address = $("#address_ent").val();
        
        $.ajax({
            url:"customer_entrep-edit.php",
            data:{id:id,name:name,ice:ice,email:email,phone:phone,address:address},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    var table = $("#entrepTable").DataTable();
                    var button = '<a href="javascript:void(0);" data-id="'+id+'" class="btn btn-primary btn-sm editEntrepBtn" ><span><i class="bi bi-pencil-square"></i></span></a> <a href = "javascript:void(0);" data-id="'+id+'" class=" btn btn-danger btn-sm deleteEntrepBtn"><span><i class="bi bi-trash"></i></span></a>';
                    var row = table.row("[id='"+tr_id+"']");
                    row.row("[id='"+tr_id+"']").data([tr_id,name,ice,email,phone,address,button]);
                    // cleaning inputs
                    $("#nom_ent").val('');
                    $("#ice").val('');
                    $("#email_ent").val('');
                    $("#phone_ent").val('');
                    $("#address_ent").val('');
                    $("#EditCusEntrepModal").modal('hide');
                }else{
                    alert('Failed: connecting with Database error');
                }
            },
        });
    });

    /**
     * delete button click for customer Entreprise Form
     */
    var entrep_deleted_id, entrep_deleted_row_id;
    $(document).on('click','.deleteEntrepBtn',function(event){
        $("#deleteEntrepModal").modal('show');
        entrep_deleted_id = $(this).data('id');
        entrep_deleted_row_id = $(this).parent().closest('tr').attr("id");
    });
    
    $(document).on('click','.deleteEntrepModalBtn',function(){
        // console.log(indv_deleted_id);
        $.ajax({
            url:'customer_entrep-delete.php',
            data:{id:entrep_deleted_id},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $('#entrepTable #'+entrep_deleted_row_id).closest('tr').remove();
                    $("#deleteEntrepModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });

    $(document).on("click","#cus_add",function(){
        if(($("#prnCusTxt").val()!="" && $("#nomCusTxt").val()!="" && $("#telCusTxt").val()!="" && $("#emailCusTxt").val()!="" && $("#adrCusTxt").val()!="") || 
        ($("#nomEntTxt").val()!="" && $("iceEntTxt").val()!="" && $("telEntTxt").val()!="" && $("#emailEntTxt").val()!="" && $("#adrEntTxt").val()!="")){
                lunchLoader();
            }
    });

    /**
     * END fetching data to individual customer table
     */

    // *****************************************************

    /**
     * fetching data to Services table
     */

    $('#servicesTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
        'createdRow':function(row,data,dataIndex){
                $(row).attr('id',data[0]);
        },
    });
    
    //  $('#servicesTable').DataTable({
    //     'info':false,
    //     'paging':true,
    //     'responsive':true,
    //     'serverSide':true,
    //     'processing':true,
    //     'autoWidth':true,
    //     // 'order':[],
    //     'ajax':{
    //         'url':'fetch_services_data.php',
    //         'type':'POST',
    //     },
    //     'createdRow':function(row,data,dataIndex){
    //         $(row).attr('id',data[0]);
    //     },
    //     'columnDefs':[{
    //         'target':[0,3],
    //         'orderable':true,
    //     }]
    // });

    $(document).on('click','.editServiceBtn',function(){
        var id = $(this).data('id');
        var tr_id = $(this).closest('tr').attr('id');
        $.ajax({
            url:'get_selected_service.php',
            data:{id:id},
            type:"post",
            success:function(data){
                var json = JSON.parse(data);
                $("#id").val(json.id);
                $("#tr_id").val(tr_id);
                $("#title").val(json.title);
                $("#servRef").val(json.ref);
                $("#prix").val(json.prix);
                $("#hiddenPrix").val(json.prix);
                $("#editServiceModal").modal('show');
            }
        });
        $.ajax({
            url:"srf.php",
            type:"POST",
            success:function(data){
                // alert(data);
                let json = JSON.parse(data);

                service_ref_Obj = {...json};
                serviceRefArr = [...Object.values(json)];
                
            }
        })
    });

    /**
     * update button click for service Form
     */
    
    $(document).on('submit','#editServiceForm',function(){
        
        var id = $("#id").val();
        var tr_id = $("#tr_id").val();
        var title = $("#title").val();
        var servRef = $("#servRef").val();
        var prix = $("#prix").val();
        var hiddenPrix = $("#hiddenPrix").val();
        
        $.ajax({
            url:"service-edit.php",
            data:{id:id,title:title,servRef:servRef,prix:prix},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    var table = $("#servicesTable").DataTable();
                    var button = '<a href="javascript:void(0);" data-id="'+id+'" class="btn btn-primary btn-sm editServiceBtn" ><span><i class="bi bi-pencil-square"></i></span></a> <a href = "javascript:void(0);" data-id="'+id+'" class=" btn btn-danger btn-sm deleteServiceBtn"><span><i class="bi bi-trash"></i></span></a>';
                    var row = table.row("[id='"+tr_id+"']");
                    // row.row("[id='"+tr_id+"']").data([tr_id,title,servRef,prix,button]);
                    row.row("[id='"+tr_id+"']").data([tr_id,title,servRef,hiddenPrix,button]);
                    // cleaning inputs
                    $("#title").val('');
                    $("#servRef").val('');
                    $("#prix").val('');
                    $("#editServiceModal").modal('hide');
                }else{
                    alert('Failed: connecting with Database error');
                }
            },
        });
    });

    /**
     * delete button click for service Form
     */
    var service_deleted_id, service_deleted_row_id;
    $(document).on('click','.deleteServiceBtn',function(event){
         $("#deleteServiceModal").modal('show');
         service_deleted_id = $(this).data('id');
         service_deleted_row_id = $(this).parent().closest('tr').attr("id");
    });
     
    $(document).on('click','.deleteServiceModalBtn',function(){
        $.ajax({
            url:'service-delete.php',
            data:{id:service_deleted_id},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $('#servicesTable #'+service_deleted_row_id).closest('tr').remove();
                    $("#deleteServiceModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });

    

    //load service data from server to check for existing Refs
    let serviceRefArr = [];
    let service_ref_Obj = [];
    if($("#serRef").length>0 || $("#addServiceRowBtn").length>0){
        // console.log('lenght service : '+$(".servRef").length)

        $.ajax({
            url:"srf.php",
            type:"POST",
            success:function(data){
                // alert(data);
                let json = JSON.parse(data);
                service_ref_Obj = {...json};

                serviceRefArr = [...Object.values(json)];
                // console.log(serviceRefArr);
                
            }
        })
    }
    $(document).on('input','.servRef_update',function(){

    $.ajax({
        url:"srf.php",
        type:"POST",
        success:function(data){
            // alert(data);
            let json = JSON.parse(data);

            service_ref_Obj = {...json};
            serviceRefArr = [...Object.values(json)];
            
        }
    })
})
    

    let submitServiceForm=false;
    $(document).on('input','.servRef',function(){
        // alert($(this).val())
       

        try{
            const existRef = serviceRefArr.some((ref)=>{
                return ref.replace(" ", "").toLowerCase() === $(this).val().replace(" ", "").toLowerCase();
            });
            // console.log(existRef);
            // alert(existRef);
            if(existRef && $(this).val().length != 0){
                throw "Ce référentiel existe déjà.";
            }else{
                $(this).removeClass("border-danger");
                $(this).addClass("border-secondary");
                $(".feedback").text("");
                submitServiceForm = false;
            }
        }catch(ex){
            $(this).addClass("border-danger");
            $(".feedback").text(ex);
            submitServiceForm = true;
        }
        
    });
    $(document).on('click','#dev_add',function(){
    if($('#devisSrvTbl tbody tr').length == 0){
     
        alert('veuillez selection au mois une service ?');

    }
    });
    // edit service editServiceBtn
    // $(document).on('input','.servRef_update',function(){
    
    // })
    
    // end

    $(document).on("click","#serv_add,#sev_update",function(){
    //    alert(submitServiceForm);
        if(submitServiceForm){
            return false;
        }
        if($(".serTitleTxt").val()!="" && $(".serTitlePrix").val()!="" && $(".serRef").val()!=""){
            lunchLoader();
        }
    });

    /**
      * END fetching data to service table
      */

    /**
     * Select Client for devis
     */
    $(document).on('click','#selectClientModal',function(){
        $("#clientShowModal").modal('show');
    });
    //fetching cus individual data
    $('#devisClientIndvTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
        
    });


    // $('#devisClientIndvTable').DataTable({
    //     'info':false,
    //     'paging':true,
    //     'responsive':true,
    //     'serverSide':true,
    //     'processing':true,
    //     'autoWidth':true,
    //     // 'order':[],
    //     'ajax':{
    //         'url':'fetch_devis_indv_cus_data.php',
    //         'type':'POST',
    //     },
    //     'createdRow':function(row,data,dataIndex){
    //         $(row).attr('id',data[0]);
    //     },
    //     'columnDefs':[{
    //         'target':[0,6],
    //         'orderable':true,
    //     }]
    // });
    // select indv button click

    $(document).on('click','.selectIndvBtn',function(){
        var id = $(this).data('id');
        // var tr_id = $(this).closest('tr').attr('id');
        $.ajax({
            url:'get_selected_cus.php',
            data:{id:id},
            type:"post",
            success:function(data){
                var json = JSON.parse(data);
                $('#client_id').val(json.customer.id);
                $("#client_type").val("individual");
                $("#receiverName").val(json.customer.prenom.toUpperCase() +' '+ json.customer.nom.toUpperCase());
                $("#receiverAdr").val(json.customer.address);
                $("#clientShowModal").modal('hide');
                // $("#tr_id").val(tr_id);
                // $("#prenom").val(json.prenom);
                // $("#nom").val(json.nom);
                // $("#email").val(json.email);
                // $("#phone").val(json.tel);
            }
        });
    });

    // fetching entreprise data

    $('#devisClientEntrepTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
        
    });

    // $('#devisClientEntrepTable').DataTable({
    //     'info':false,
    //     'paging':true,
    //     'responsive':true,
    //     'serverSide':true,
    //     'processing':true,
    //     'autoWidth':true,
    //     // 'order':[],
    //     'ajax':{
    //         'url':'fetch_devis_entrep_cus_data.php',
    //         'type':'POST',
    //     },
    //     'createdRow':function(row,data,dataIndex){
    //         $(row).attr('id',data[0]);
    //     },
    //     'columnDefs':[{
    //         'target':[0,6],
    //         'orderable':true,
    //     }]
    // });

    // select entrep button click
    $(document).on('click','.selectEntrepBtn',function(){
        var id = $(this).data('id');
        // var tr_id = $(this).closest('tr').attr('id');
        $.ajax({
            url:'get_selected_entrepCus.php',
            data:{id:id},
            type:"post",
            success:function(data){
                var json = JSON.parse(data);
                // console.log(json.customer);

                $('#client_id').val(json.customer.id);
                $("#client_type").val("entreprise");
                $("#receiverName").val(json.customer.nom.toUpperCase());
                const br = document.createElement("br");
                $("#receiverAdr").val(`${json.customer.address} \n ${json.customer.ICE}`);
                $("#clientShowModal").modal('hide');
                // $("#id_ent").val(json.id);
                // $("#tr_id_ent").val(tr_id);
                // $("#nom_ent").val(json.nom);
                // $("#ice").val(json.ICE);
                // $("#email_ent").val(json.email);
                // $("#phone_ent").val(json.tel);
                // $("#address_ent").val(json.address);
            }
        });
    });

    // service Pirce onchange

    $(document).on('input','#servicesListId',function(){
        try{

            var thisValue = $(this).val().replace(/(['"])/g, "");
            /**
             * replace it with this value to replace the " with \"
             * var thisValue = $(this).val().replace(/(['"])/g, "\\$1");
             */
            var opt = $('option[value="'+ thisValue + '"]');
            service_id = opt.length ? opt.attr('id') : ' ';
            var thisElm = $(this);
            if(service_id != ' ' && thisValue != ''){
                $.ajax({
                    url:"get_selected_price.php",
                    type:'POST',
                    data:{service_id:service_id},
                    success:function(data){
                        var json = JSON.parse(data);
                        thisElm.parent().parent().children().eq(4).children('.servicePrice').val(json.prix);
                        // Add ref to services
                        thisElm.parent().parent().children().eq(1).children('.servRefTxt').val(json.ref);
                    }
                });
            }else{
                thisElm.parent().parent().children().eq(4).children('.servicePrice').val('');
                thisElm.parent().parent().children().eq(1).children('.servRefTxt').val('');
            }
            setTimeout(rowTotal,700);
            //trigger onchange event to check for existing
            $('.servRefTxt').trigger('change');
        }catch(ex){
            console.log(ex.message);
        }
    });

    $(document).on("change",'#servicesListId',function(){
        $('.servRefTxt').trigger('change');
    });

    // function checkForRefServiceExist(thisElement){
    //     const srvVal = thisElement.val().trim();
    //     const refVal = thisElement.parent().children().closest('.servRefTxt');
    //     // console.log(thisServiceObj);

    //     //check if service exist in DB
    //     const existService = [...Object.keys(service_ref_Obj)].some((srv)=>{
    //         return srv.replace(" ", "").toLowerCase() === srvVal.replace(" ", "").toLowerCase();
    //     });
    //     //check if ref exist in DB
    //     const existRef = [...Object.values(service_ref_Obj)].some((ref)=>{
    //         return ref.replace(" ", "").toLowerCase() === refVal.val().trim().replace(" ", "").toLowerCase();
    //     });

    //     if(existService && existRef){
    //         return;
    //     }
    // }

    
    //on ref input to check for existing refs
    $(document).on("input change",".servRefTxt",function(){        

        const existRef = serviceRefArr.some((ref)=>{
            return ref.replace(" ", "").toLowerCase() === $(this).val().replace(" ", "").toLowerCase();
        });

        const srvVal = $(this).next('#servicesListId').val();
        let exist = false;

        // loop over all services and their ref and check if the value of the inputs are equal if true return else will check for existing refs
        for(let srv in service_ref_Obj){
            if($(this).val().replace(" ", "").toLowerCase() === service_ref_Obj[srv].replace(" ", "").toLowerCase() && 
                srv.replace(" ", "").toLowerCase() == srvVal.replace(" ", "").toLowerCase() && srvVal != "")
            {
                exist = true;
                break;
            }
        }

        if(exist){
            $(this).removeClass("border-danger");
            $(this).popover("hide");
            return;
        }

        if(existRef && $(this).val().length != 0){
            $(this).addClass("border-danger");
            $(this).css({
                "outline": "red"
            });
            $(this).popover("show");
            submitServiceForm = true;
        }else{
            $(this).removeClass("border-danger");
            $(this).popover("hide");
            $(this).css({
                "outline": "none"
            });
            submitServiceForm = false;
        }
        
    });
    
     
    
    
    //add service row click
    $(document).on('click','.deleteRowBtn',function(e){
        e.preventDefault();
        $(this).closest('tr').remove();
        rowTotal();
    });

    //Column montant Display value
    $(document).on("input",".servicePrice",function(){
        rowTotal();
        brkRowTotal();

    });
  
    // client
    $(document).on("input",".rowServiceQte",function(){
        rowTotal();
        
    });
   
    
    $(document).on("input",".serviceDiscount",function(){
        rowTotal();
        brkRowTotal();

    });
    $(document).on("change",".removeTva",function(){
        rowTotal();
        brkRowTotal();

    });

     // broker
     $(document).on("input",".rowBrkServiceQte",function(){    
        brkRowTotal();
        // console.log('hi');
    });
    $(document).on("input",".serviceBrkPrice",function(){    
        // console.log('hi');
        brkRowTotal();
    });
    $(document).on("input",".serviceBrkDiscount",function(){    
        // console.log('hi');
        brkRowTotal();
    });
    // calculate row price function and Total price For "devis"
    // function rowTotal(){
    //     var grand_total = 0,
    //     disc = 0;
    //     $('.servicesTable tbody tr').each(function(){
    //         var rowQte = $('.rowServiceQte',this).val(),
    //         rowPrice = $('.servicePrice',this).val(),
    //         rowDiscount = $('.serviceDiscount',this).val(),
    //         rowMontant = $('.rowServiceTotal',this);
    //         var discount = (rowDiscount == "") ? 0 : parseFloat(rowDiscount)/100;
    //         var originalPrice = parseFloat(rowQte)*parseFloat(rowPrice);
    //         var res = isNaN(originalPrice)? 0 : (originalPrice - (originalPrice*discount)).toFixed(2);
    //         rowMontant.val(res);

    //         grand_total += parseFloat(rowMontant.val());
    //         disc += originalPrice - res;

    //     });
    //     $(".labelSubTotal").text(grand_total.toFixed(2)+" DH");
    //     $(".labelDiscount").text(isNaN(disc) ? `${0} DH` : `${disc.toFixed(2)} DH`);

    //     var tva = 0.2;
    //     var price_Tva = (grand_total*tva) + grand_total;
    //     var addedTvaPrice = price_Tva - grand_total

    //     $('.labelTva').text(addedTvaPrice.toFixed(2)+" DH");
    //     $('.labelDevisTotal').text(price_Tva.toFixed(2)+" DH");

    //     if($('.removeTva').is(':checked')){
    //         $('.labelTva').text('0.00 DH');
    //         $('.labelDevisTotal').text(grand_total.toFixed(2)+" DH");
    //     }
    // }

    var selectedDevisBroker=false;
    $(document).on('click',".selectDevisBrkBtn",function(){
         // show icon to remove Broker
         $("#removeBkr").removeClass("d-none");
         $("#removeBkr").addClass("d-block");
    
        //  end
        let brkId = $(this).data('id');
        let brkName = $(this).data('nom');
        $("#selectedBrkId").val(brkId);
        // add name of broker selected
        $("#selectedBrkName").val(brkName);


        selectedDevisBroker=true;
        $("#devisBrokerModal").modal("hide");
    });
    // remove Broker selected in Devis
    $(document).on('click',"#removeBkr",function(){
        // empty input of idBroker
        $("#selectedBrkId").val('');
        // empty input of NameBroker
        $("#selectedBrkName").val('Intermédiaire');
            // make selected broker false
            selectedDevisBroker=false;
            // remove this icon of remove broker
            $(this).removeClass('d-block');
            $(this).addClass('d-none');
         

          
        

    })

    let dBrk_id = '';

    // send data to devis-add on create devis button click
    $(document).on('submit','#devisForm',function(e){
        
        if(submitServiceForm){
            return false;
        }
        
        var tableData = new Array();
        var row =0;
        $('.servicesTable tbody tr').each(function(){
            var service_name = $(".serviceDropdown",this).val();
            srvRef = $(".servRefTxt",this).val();
            qte = $('.rowServiceQte',this).val();
            price = $('.servicePrice',this).val();
            discount = $(".serviceDiscount",this).val();
            unit = $(".serviceUnit",this).val();
            montant=$(".rowServiceTotal",this).val();
            tableData[row] = {
                "serviceName":service_name,
                "quantity":qte,
                "price":price,
                "discount":discount,
                "unit":unit,
                "srvRef":srvRef,
                "montant":montant
            }
            row++;
        });
        if($("#client_id").val()!="" && tableData.length != 0){
            tableData = JSON.stringify(tableData);
            var client_id = $('#client_id').val(),
            client_type = $("#client_type").val(),
            tva_checked = $('.removeTvaClient').is(':checked'),
            devis_number = $('#devis_number').val(),
            devis_comment = $("#devis_comment").val(),
            labelSubTotal = $('#labelSubTotal').text(),
            labelDiscount = $('#labelDiscount').text(),
            labelDevisTotal = $('#labelDevisTotal').text(),
            objet_name = $("#objet_name").val(),
            located_txt = $("#sisTxt").val();
            // console.log(labelDevisTotal);

            let brkId;
            if(selectedDevisBroker && $("#selectedBrkId").val()!=""){
                brkId = $("#selectedBrkId").val();
            }
            
            lunchLoader();

            $.ajax({
                url:'devis-add.php',
                type:"POST",
                data:{tableData:tableData,client_id:client_id,client_type:client_type,devis_number:devis_number,devis_comment:devis_comment,labelSubTotal:labelSubTotal,labelDiscount:labelDiscount,labelDevisTotal:labelDevisTotal,tva_checked:tva_checked,objet_name:objet_name,located_txt:located_txt,brkId:brkId},
                success:function(data){
                    // alert(data);
                    // if(status == 'success')
                    // {
                        //     alert(data);
                        // }
                        // alert("all good");
                        // location.href = "devis.php";
                        var json = JSON.parse(data);
                        // alert(json.status +'brk is'+selectedDevisBroker)
                        var status = json.status;
                        
                        //TODO use this id in add those prices to DB
                        //devis id from the devis_add 
                        dBrk_id = json.dBrk_id;
                        devis_id = json.devis_id;
                        
                        //set condition if broker empty or not
                        if(status == 'success' && selectedDevisBroker){
                            
                            // if(selectedDevisBroker){
                                //hide table rows && all the labels to prevent the rowTotal function runing on client devis
                      $("#devisSrvTbl tbody tr").remove();
                        $('#labelSubTotal ,#labelDiscount ,#labelTva ,#labelDevisTotal').addClass("invisible");
                        // $('#labelDiscount').text(" ");
                        // $('#labelTva').text(" ");
                        // $('#labelDevisTotal').text(" ");
                        //initialize broker devis
                        $(".loader-wrapper").addClass("loader-hidden");
                        $("#devisBrokerViewModal").modal("show");
                        //setting values
                        $("#brkReceiverName").val($("#receiverName").val());
                        $("#brkReceiverAdr").val($("#receiverAdr").val());
                        $("#brkDevis_number").val($("#devis_number").val());
                        $("#brk_dateTxt").val($("#dateTxt").val());
                        $("#BrkObjet_name").val($("#objet_name").val());
                        $("#brkSisTxt").val($("#sisTxt").val());
                        $("#brkDevis_comment").val($("#devis_comment").val());
                        // devis_id
                        
                        // alert(devis_id);
                   
                        // $(".labelSubTotal_broker");
                        // console.log($(".labelSubTotal_broker").text())
                        
                       

                        //insert data in service table
                        let html = ``;
                        let devisTableData = JSON.parse(tableData);
                        // console.log(devisTableData);
                        for (let i = 0; i < devisTableData.length; i++) {
                            if(devisTableData[i]["srvRef"] != ""){
                                // console.log(devisTableData[i]["unit"]);
                                html += `<tr>`;
                                html += `<td></td>`;
                                html += `<td class="input-group"><input type="text" class="input-group-text w-25 servRefTxt" id="srvRT" value="${devisTableData[i]["srvRef"]}" placeholder="Reference" autocomplete="off" required data-bs-placement="bottom" data-bs-content="Cette référence existe déjà" data-bs-trigger="manual" data-bs-custom-class="error-popover" disabled><input type="text" id="servicesListId" list="servicesList"  autocomplete="off" value="${devisTableData[i]["serviceName"]}" class="form-control serviceDropdown" aria-describedby="srvRT" disabled><datalist id="servicesList"></datalist></td>`;
                                html += `<td><input style="width: 50px;" type="text" name="" class="form-control py-1 serviceUnit" value="${devisTableData[i]["unit"]}"  placeholder="Unité" disabled></td>`;
                                html += `<td><input type="number" min="0" name="" class="form-control py-1 px-1 rowServiceQte rowBrkServiceQte"  value="${devisTableData[i]["quantity"]}" placeholder="Quantité" disabled></td>`;
                                html += `<td><input type="number" min="0"  step="0.01" name="" class="form-control py-1 px-1 servicePrice serviceBrkPrice"  value="${devisTableData[i]["price"]}" placeholder="0.00"  ></td>`;
                                html += `<td><div class="input-group"><span style="width: 30px;" class="input-group-text py-1"><i class="bi bi-percent"></i></span><input style="width: 38px;" type="number"  min="0" name="" value="${devisTableData[i]["discount"]}" class="form-control py-1 serviceDiscount serviceBrkDiscount" placeholder="Enter % (ex: 10%)" ></div></td>`;
                                html += `<td><input type="text" name="" class="form-control py-1 rowServiceTotal rowServiceBrkTotal" value="${devisTableData[i]["montant"]}" disabled placeholder="0" disabled></td>`;
                               html += `<td><input type="hidden" name="srv_unique_id" id="srv_unique_id" class="form-control py-1 serviceUniqueId" disabled value="${devis_id+i+1}"></td>`;

                                html += `</tr>`;
                            }
                            
                        }
                        $("#devisBrkShowTable tbody").html(html);
                        if(tva_checked){
                            $("#BrkTvaCheckbox").prop("checked",true);
                        }
                        // rowTotal();
                        $('.removeTva_broker').prop('checked', true);
                        brkRowTotal();

                        

                    }
                    else if(status=='success' && selectedDevisBroker==false){
                   
                        location.href='devis-view.php?sc=sucadd';
                    }
                    

                },
                errro:function(err){
                    console.log(err);
                }
            });
            if(selectedDevisBroker){
                return false;
            }
                      $("#devisSrvTbl tbody tr").remove();
        }
                      $("#devisSrvTbl tbody tr").remove();
    });


    $(document).on('click','.btn_brk_devis_confirm',function(){
       if(dBrk_id != '') {
        let prices = [];
        $('#devisBrkShowTable tbody tr').each(function(){
            // prices
            var price = {
                "price":$('.servicePrice',this).val(),
                "discount":$('.serviceDiscount',this).val(),
                "service_unique_id":$('.serviceUniqueId',this).val(),
            }
            
            prices.push(price);
        
        });
        $.ajax({
            url:"devis_brk_dets.php",
            type:'POST',
            data:{dBrk_id:dBrk_id,devis_id:devis_id,prices:prices},
            success:function(data){
                // alert(data);
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    location.href='devis-view.php?sc=sucadd';
                }
            }
        });
       }
    });
    // btn broker edit devis
    $(document).on('click','#updateBrkDevis',function(e){
        // e.preventDefault();
        var DevisId = $('#devis_id').val();
        // var brkId =   $('#broker_id').val();
        
        // var uniqueServiceId =   $('#uniqueService_id').val();
        var devis_broker_id =$('#devis_broker_id').val();
        // alert(devis_broker_id);
        if(devis_broker_id != '') {
         let prices = [];
        //  let discounts = [];
        //  let service_unique_ids = [];
         $('.devisShowTableBrk tbody tr').each(function(){
            
        //      // prices
             var price = {
                 "price":$('.serviceBrkPrice',this).val(),
                 "discount":$('.serviceBrkDiscount',this).val(),
                 "service_unique_id":$('.serviceUniqueId',this).val(),
             }
             
             prices.push(price);
            //  console.log(prices);
            //  alert('stop');
         
         });
         $.ajax({
             url:"devis_brk_dets_update.php",
             type:'POST',
             data:{dBrk_id:devis_broker_id,devis_id:DevisId,prices:prices},
             success:function(data){
                // alert(data);
                 var json = JSON.parse(data);
                 var status = json.status;
                 if(status == 'success'){
                     location.href='devis-view.php?sc=sucupd';
                 }
             }
         });
        }
     });

    //fetching devis data

    $('#devisTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        'order':[[4,'desc']],
        "pagingType": "input",
    });
    // rowTotal();
    // $(window).on("pageshow",rowTotal);
// **********************
    // window.addEventListener ? 
    // window.addEventListener("load",rowTotal,false) : 
    // window.attachEvent && window.attachEvent("onload",rowTotal);
// **********************

    // $(window).on('load',function (){
    //     rowTotal();
    // })
    // window.onload = rowTotal;
    // $('#devisShowTable input').prop('disabled',true);
    $('#devisShowTable .deleteRowBtn').css("display","none");
    // $('#devisShowTable .deleteRowBtn').prop('disabled',true);
    //btn View devis Click 

    // $(document).on('click',".viewDevisBtn",function(){
    //     alert('stop');
    //     brkRowTotal();

    // });
        
    //     var devis_id = $(this).data('id'),
    //     client_id = $(this).data("id_client");
    //     // console.log(client_id);
        
    //     $.ajax({
    //         url:'devis-show.php',
    //         type:'POST',
    //         data:{devis_id:devis_id,client_id:client_id},
    //         success:function(data){
    //             //redirect to devis-show
                
                
    //             // var json = JSON.parse(data);
    //             // // console.log(json);
    //             // json.forEach(row => {
    //             //     html = '';
    //             //     html += '<tr>';
    //             //     html += '<td><i class="bi bi-trash fs-5 deleteRowBtn"></i></td>';
    //             //     html += '<td><input type="text" id="servicesListId" list="servicesList" autocomplete="off" value="'+row[2]+'" class="form-control serviceDropdown"><datalist id="servicesList"><?php echo fill_service_dropDown(); ?></datalist></td>';
    //             //     html += '<td><input type="text" name="" class="form-control py-1"  placeholder="Unité"></td>';
    //             //     html += '<td><input type="number" min="0" name="" class="form-control py-1 px-1 rowServiceQte" value="'+row[4]+'" placeholder="Quantité"></td>';
    //             //     html += '<td><input type="number" min="0"  step="0.01" name="" class="form-control py-1 px-1 servicePrice" value="'+row[3]+'" placeholder="0.00"></td>';
    //             //     html += '<td><div class="input-group"><span class="input-group-text py-1"><i class="bi bi-percent"></i></span><input type="number" min="0" name="" value="'+row[5]+'" class="form-control py-1 serviceDiscount" placeholder="Enter % (ex: 10%)"></div></td>';
    //             //     html += '<td><input type="text" name="" class="form-control py-1 rowServiceTotal" disabled placeholder="0"></td>';
    //             //     html += '</tr>';
    //             //     $('.devisShowTable tbody').append(html);
    //             // });
                
    //         }
    //     });
    //     // location.href = "devis-show.php";
    // });


    //************************* */
    //devis update

    $(document).on("submit","#devisEditForm",function(e){
        // rowTotal();
        //checking if services ref not exist

        if(submitServiceForm){
            return false;
        }
        //sending data to devis-update
        var tableData = new Array();
        var row =0;
        $('.servicesTable tbody tr').each(function(){
            var service_name = $(".serviceDropdown",this).val(),
            srvRef = $(".servRefTxt",this).val(),
            qte = $('.rowServiceQte',this).val(),
            price = $('.servicePrice',this).val(),
            discount = $(".serviceDiscount",this).val(),
            unit = $(".serviceUnit",this).val(),
            serviceUniqueId=$('.serviceUniqueId',this).val();
        
            tableData[row] = {
                "serviceName":service_name,
                "quantity":qte,
                "price":price,
                "discount":discount,
                "unit":unit,
                "srvRef":srvRef,
                "serviceUniqueId":serviceUniqueId
            }
            row++;
        });
        // alert(0);
        if($("#devis_id").val()!="" && tableData.length != 0){
            tableData = JSON.stringify(tableData);
            // console.log(tableData);
            var client_id = $('#client_id').val(),
            devis_id = $('#devis_id').val(),
            devis_comment = $("#devis_comment").val(),
            labelSubTotal = $('#labelSubTotal').text(),
            labelDiscount = $('#labelDiscount').text(),
            labelDevisTotal = $('#labelDevisTotal').text(),
            tva_checked = $('.removeTvaClient').is(':checked'),
            // devisStatus = $('#devisStatusDropdown').val(),
            
            objet_name = $("#objet_name").val(),
            located_txt = $("#sisTxt").val();

            
            let brkId;
            if($("#selectedBrkId").val()!=""){
                brkId = $("#selectedBrkId").val();
                // console.log(brkId);
            }
            // alert(located_txt);
            // console.log(1);
            // alert(1);
            lunchLoader();
                  // admin or not
           admin= $('#devis_id').data('admin');
           if(admin==1){
            devisStatus = $('#devisStatusDropdown').val();
 
 
           }else{
            devisStatus = $('.devisStatusDropdown').val();
 
           }
        //    console.log(devisStatus);

            $.ajax({
                url:'devis-update.php',
                type:"POST",
                data:{tableData:tableData,client_id:client_id,devis_comment:devis_comment,labelSubTotal:labelSubTotal,labelDiscount:labelDiscount,labelDevisTotal:labelDevisTotal,devisStatus:devisStatus,devis_id:devis_id,objet_name:objet_name,located_txt:located_txt,tva_checked:tva_checked,brkId:brkId},
                success:function(data){
                    // alert((data));
                    // alert('az');
                    var json = JSON.parse(data);
                    var status = json.status;
                    
                    dBrk_id = json.dBrk_id;
                    devis_id = json.devis_id;
                    
                    // if(status == 'success'){
                        //     location.href='devis-view.php?sc=sucupd';
                        // }
                    if(status == 'success' && dBrk_id!=0){
                            
                        // if(selectedDevisBroker){
                            //hide table rows && all the labels to prevent the rowTotal function runing on client devis
                            // alert($(".servicesTable tbody tr"));
                  $(".servicesTable tbody tr").remove();
                    $('#labelSubTotal ,#labelDiscount ,#labelTva ,#labelDevisTotal').addClass("invisible");
                    // $('#labelDiscount').text(" ");
                    // $('#labelTva').text(" ");
                    // $('#labelDevisTotal').text(" ");
                    //initialize broker devis
                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#devisBrokerViewModal_update").modal("show");
                    //setting values
                    $("#brkReceiverName").val($("#receiverName").val());
                    $("#brkReceiverAdr").val($("#receiverAdr").val());
                    $("#brkDevis_number").val($("#devis_number").val());
                    $("#brk_dateTxt").val($("#dateTxt").val());
                    $("#BrkObjet_name").val($("#objet_name").val());
                    $("#brkSisTxt").val($("#sisTxt").val());
                    $("#brkDevis_comment").val($("#devis_comment").val());
                    // devis_id
                    
                    // alert(devis_id);
               
                    // $(".labelSubTotal_broker");
                    // console.log($(".labelSubTotal_broker").text())
                    
                   

                    //insert data in service table
                    let html = ``;
                    let devisTableData = JSON.parse(tableData);
                    // console.log(devisTableData);
                    for (let i = 0; i < devisTableData.length; i++) {
                        uniqueSrvice_id=parseInt(devis_id)+i+1;
                        if(devisTableData[i]["srvRef"] != ""){
                            // console.log(devisTableData[i]["unit"]);
                            html += `<tr>`;
                            html += `<td></td>`;
                            html += `<td class="input-group"><input type="text" class="input-group-text w-25 servRefTxt" id="srvRT" value="${devisTableData[i]["srvRef"]}" placeholder="Reference" autocomplete="off" required data-bs-placement="bottom" data-bs-content="Cette référence existe déjà" data-bs-trigger="manual" data-bs-custom-class="error-popover" disabled><input type="text" id="servicesListId" list="servicesList"  autocomplete="off" value="${devisTableData[i]["serviceName"]}" class="form-control serviceDropdown" aria-describedby="srvRT" disabled><datalist id="servicesList"></datalist></td>`;
                            html += `<td><input style="width: 50px;" type="text" name="" class="form-control py-1 serviceUnit" value="${devisTableData[i]["unit"]}"  placeholder="Unité" disabled></td>`;
                            html += `<td><input type="number" min="0" name="" class="form-control py-1 px-1 rowServiceQte rowBrkServiceQte"  value="${devisTableData[i]["quantity"]}" placeholder="Quantité" disabled></td>`;
                            html += `<td><input type="number" min="0"  step="0.01" name="" class="form-control py-1 px-1 servicePrice serviceBrkPrice"  value="${devisTableData[i]["price"]}" placeholder="0.00"  ></td>`;
                            html += `<td><div class="input-group"><span style="width: 30px;" class="input-group-text py-1"><i class="bi bi-percent"></i></span><input style="width: 38px;" type="number"  min="0" name="" value="${devisTableData[i]["discount"]}" class="form-control py-1 serviceDiscount serviceBrkDiscount" placeholder="Enter % (ex: 10%)" ></div></td>`;
                            html += `<td><input type="text" name="" class="form-control py-1 rowServiceTotal rowServiceBrkTotal" value="${devisTableData[i]["montant"]}" disabled placeholder="0" disabled></td>`;
                           html += `<td ><input type="hidden" name="srv_unique_id" id="srv_unique_id" class="form-control py-1 serviceUniqueId" disabled value="`+(uniqueSrvice_id)+`"></td>`;

                            html += `</tr>`;
                        }
                        
                    }
                    $("#devisBrokerViewModal_update tbody").html(html);
                    // if(tva_checked){
                    //     $("#BrkTvaCheckbox").prop("checked",true);
                    // }
                    // rowTotal();
                    $('.removeTva_broker').prop('checked', true);
                    brkRowTotal();


                }else{
                    location.href = 'devis-view.php?sc=sucadd';
                }
               

                },
                errro:function(err){
                    console.log(err);
                }
                
            });
           
                return false;
            
            
            // alert("ze");
            
        }
    });
    $(document).on('click', '.btn_brk_devis_confirm_update', function() {

        if (dBrk_id != '') {
            let prices = [];
            $('#devisBrkShowTable tbody tr').each(function() {
                var price = {
                    "price": $('.servicePrice', this).val(),
                    "discount": $('.serviceDiscount', this).val(),
                    "service_unique_id": $('.serviceUniqueId', this).val()
                };
                prices.push(price);
            });
            devis_id=$('#devis_id').val()
            // console.log();

            // console.log(prices);
    
            $.ajax({
                url:"devis_brk_dets_delete_add.php",
                type: 'POST',
            data:{dBrk_id:dBrk_id,devis_id:devis_id,prices:prices},
                
                success: function(data) {
                    // alert(data);
                    var json = JSON.parse(data);
                    var status = json.status;
                    if (status == 'success') {
                        location.href = 'devis-view.php?sc=sucadd';
                    }
                },
                error: function(xhr, status, error) {
                    alert(error);
                }
            });
        }
    });
    
    //devis delete

    var devis_deleted_id, devis_deleted_row;
    $(document).on('click','.deleteDevisBtn',function(event){
        $("#deleteDevisModal").modal('show');
        devis_deleted_id = $(this).data('id');
        devis_deleted_row = $(this);
    });
     
    $(document).on('click','.deleteDevisModalBtn',function(){
        $.ajax({
            url:'devis-delete.php',
            data:{id:devis_deleted_id},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $(devis_deleted_row).closest('tr').remove();
                    $("#deleteDevisModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });

    // fetching roles data
    $('#rolesTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
    });

    //role Delete

    var role_deleted_id, role_deleted_row;
    $(document).on('click','.deleteRoleBtn',function(event){
        $("#deleteRoleModal").modal('show');
        role_deleted_id = $(this).data('id');
        role_deleted_row = $(this);
    });
     
    $(document).on('click','.deleteRoleModalBtn',function(){
        $.ajax({
            url:'role-delete.php',
            data:{id:role_deleted_id},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $(role_deleted_row).closest('tr').remove();
                    $("#deleteRoleModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });

    $(document).on("click","#rl_add",function(){
        if($("#roleNameText").val()!=""){
            lunchLoader();
        }
    });
    $(document).on("click","#rl_create",function(){
        lunchLoader();
    });
    $(document).on("click","#updt_rl",function(){
        lunchLoader();
    });
    $(document).on("click",".editRoleBtn",function(){
        lunchLoader();
    });


    // check phone input for users
    $(document).on('input','#userPhoneText',function(){
        if($(this).val().length>15){
            $(this).addClass('border-error');
            var html = '<p class="text-danger text_error">phone number shouldn\'t pass 15 character</p>'
            if(!$('.text_error').length){
                $(this).parent().append(html);
            }
        }else{
            $(this).removeClass('border-error');
            $(this).parent().children('p').remove();
        }
    });

    // fetching Users data
    $('#usersTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
    });

    //User Delete

    var user_deleted_id, user_deleted_row;
    $(document).on('click','.deleteUserBtn',function(event){
        $("#deleteUserModal").modal('show');
        user_deleted_id = $(this).data('id');
        user_deleted_row = $(this);
    });
     
    $(document).on('click','.deleteUserModalBtn',function(){
        $.ajax({
            url:'user-delete.php',
            data:{id:user_deleted_id},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $(user_deleted_row).closest('tr').remove();
                    $("#deleteUserModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });

    $(document).on("click","#usr_add",function(){
        if($("#prenomText").val()!="" && $("#nomText").val()!="" && $("#emailText").val()!="" && $("#userPhoneText").val() != "" && $("#usernameText").val()!="" && $("#passwordText").val()!="" && $("#roleSelect").val() != null ){
            lunchLoader();
        }
    });
  
    

    //notifications table
    $('#notificationTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        'order':[[3,'desc']],
        "pagingType": "input",
    });

   
    //client approve devis & invoice click
    $(document).on('click','.btn-client-approve',function(){
        var doc_id = $(this).data('id');
        var srv_unique_id = $(this).data('srv_unique_id');
        // console.log($(this).data());
        var doc_type = $("#doc_type").val();
        
        var btn=$(this);
        lunchLoader();
        $.ajax({
            url:"service_approve.php",
            data:{doc_id:doc_id,doc_type:doc_type,srv_unique_id:srv_unique_id},
            type:"POST",
            success:function(data){
                // alert(data);
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $(".loader-wrapper").addClass("loader-hidden");
                    btn.closest("tr").addClass("approved_row");
                    // console.log($('#devisShowTable tr#'+ doc_id));
                    $('#devisShowTable tr#'+doc_id).addClass('approved_row');
                    $('#devisShowTableBrk tr#'+doc_id).addClass('approved_row');
                   // Refresh the page after a specific time delay (e.g., 2000 milliseconds or 2 seconds)
                   
                   setTimeout(()=>$(".loader-wrapper").remove(),1000);

                    // btn.closest("tr").css("background"," #bcf5bc");
                    // btn.css("display","none");
                    btn.parent().append(`<span><i class="bi bi-x-circle btn btn-outline-danger btn-sm rounded-circle btn-cancel-client-approve" data-id="${doc_id}" data-srv_unique_id="${srv_unique_id}" ></i></span>`)
                    btn.remove();
                    // $(".btn-cancel-client-approve").css("display","inline-block");
                }
            }
        });
        setTimeout(()=>$(".loader-wrapper").remove(),2000);
    });
    //cancel client approve devis Click
    $(document).on("click",".btn-cancel-client-approve",function(){
        const doc_id = $(this).data('id');
        var srv_unique_id = $(this).data('srv_unique_id');
        // console.log(srv_unique_id);
        var btn=$(this);
        lunchLoader();
        $.ajax({
            url:"can_clA.php",
            data:{doc_id:doc_id,srv_unique_id:srv_unique_id},
            type:"POST",
            success:function(data){
                // alert(data)
               
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $(".loader-wrapper").addClass("loader-hidden");
                    // btn.closest("tr").css("background-color","red");
                    btn.closest("tr").removeClass("approved_row");
                    $('#devisShowTable tr#'+doc_id).removeClass('approved_row');
                    $('#devisShowTableBrk tr#'+doc_id).removeClass('approved_row');
                    // Refresh the page after a specific time delay (e.g., 2000 milliseconds or 2 seconds)
                    setTimeout(()=>$(".loader-wrapper").remove(),1000);


                    // btn.css("display","none");
                    // $(".btn-client-approve").css("display","inline-block");
                    btn.parent().append(`<span><i class="bi bi-check-circle btn btn-outline-success btn-sm rounded-circle btn-client-approve" data-id="${doc_id}" data-srv_unique_id="${srv_unique_id}" ></i></span>`)
                    btn.remove();
                }
            }
        });
        setTimeout(()=>$(".loader-wrapper").remove(),2000);
    });

    // send data to invoice-add on create Facture button click
    $(document).on('submit','#invoiceForm',function(e){
        // e.preventDefault();
        var tableData = new Array();
        var row =0;
        $('.servicesTable tbody tr').each(function(){
            var service_name = $(".serviceDropdown",this).val(),
            srvRef = $(".servRefTxt",this).val(),
            qte = $('.rowServiceQte',this).val(),
            price = $('.servicePrice',this).val(),
            discount = $(".serviceDiscount",this).val(),
            unit = $(".serviceUnit",this).val();
        
            tableData[row] = {
                "serviceName":service_name,
                "quantity":qte,
                "price":price,
                "discount":discount,
                "unit":unit,
                "srvRef":srvRef
            }
            row++;
        });
        if($("#client_id").val()!="" && tableData.length != 0){
            tableData = JSON.stringify(tableData);
            var client_id = $('#client_id').val(),
            client_type = $("#client_type").val(),
            tva_checked = $('.removeTva').is(':checked'),
            invoice_number = $('#invoice_number').val(),
            invoice_comment = $("#invoice_comment").val(),
            labelSubTotal = $('#labelSubTotal').text(),
            labelDiscount = $('#labelDiscount').text(),
            labelInvoiceTotal = $('#labelDevisTotal').text(),
            invoice_payment = $("#invoice_payment").val(),
            invoice_pay_giver = $("#invoice_payment_giver").val(),
            payment_method = $("#pay_method").val(),
            due_date = $("#due_date").val(),
            objet_name = $("#objet_name").val(),
            located_txt = $("#sisTxt").val();
            
            lunchLoader();
            
            $.ajax({
                url:'invoice-add.php',
                type:"POST",
                data:{tableData:tableData,client_id:client_id,client_type:client_type,invoice_number:invoice_number,invoice_comment:invoice_comment,labelSubTotal:labelSubTotal,labelDiscount:labelDiscount,labelInvoiceTotal:labelInvoiceTotal,tva_checked:tva_checked,invoice_payment:invoice_payment,payment_method:payment_method,invoice_pay_giver:invoice_pay_giver,due_date:due_date,objet_name:objet_name,located_txt:located_txt},
                success:function(data){ 
                    // alert(data);
                    console.log(data);                  
                    location.href='invoice-list.php?sc=sucadd'; 
                    var json = JSON.parse(data);
                    var status = json.status;
                    if(status == 'success'){
                    
                        
                        location.href='invoice-list.php?sc=sucadd';
                        
                    }
                }
            });
        }
    });

    $(document).on("click",".btn-save-payment",function(e){
        e.preventDefault();
        saveInvoicePayment();
        
    });



    /**
     * invoice prix section
     */
    $(document).on("click",".btn-close-inv-pay",function(){
        $(".btn-invoice-payment").click();
    });

    //fetching invoice data

    $('#invoiceTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        'order':[[4,'desc']],
        "pagingType": "input",
    });

    //invoice view disable delete btn
    $('#invoiceShowTable input').prop('disabled',true);
    $('#invoiceShowTable .deleteRowBtn').css("display","none");


    //invoice update

    $(document).on("submit","#invoiceEditForm",function(e){
        // e.preventDefault();
        var tableData = new Array();
        var row =0;
        $('.servicesTable tbody tr').each(function(){
            var service_name = $(".serviceDropdown",this).val(),
            srvRef = $(".servRefTxt",this).val(),
            qte = $('.rowServiceQte',this).val(),
            price = $('.servicePrice',this).val(),
            discount = $(".serviceDiscount",this).val(),
            unit = $(".serviceUnit",this).val();
        
            tableData[row] = {
                "serviceName":service_name,
                "quantity":qte,
                "price":price,
                "discount":discount,
                "unit":unit,
                "srvRef":srvRef
            }
            row++;
        });
        // alert(0);
        if($("#invoice_id").val()!="" ){
            tableData = JSON.stringify(tableData);
            var client_id = $('#client_id').val(),
            invoice_id = $('#invoice_id').val(),
            invoice_comment = $("#invoice_comment").val(),
            labelSubTotal = $('#labelSubTotal').text(),
            labelDiscount = $('#labelDiscount').text(),
            labelDevisTotal = $('#labelDevisTotal').text(),
            due_date = $("#due_date").val(),
            // admin or not
           admin= $('#invoice_id').data('admin');
          if(admin==1){
            invoiceStatus = $('#invoiceStatusDropdown').val();


          }else{
            invoiceStatus = $('.invoiceStatusDropdown').val();

          }
            console.log('status'+invoiceStatus)
            console.log('inv_id'+invoice_id)
            objet_name = $("#objet_name").val(),
            located_txt = $("#sisTxt").val();
            // console.log(1);
            // alert(1);
            lunchLoader();

            $.ajax({
                url:'invoice-update.php',
                type:"POST",
                data:{tableData:tableData,client_id:client_id,invoice_comment:invoice_comment,labelSubTotal:labelSubTotal,labelDiscount:labelDiscount,labelDevisTotal:labelDevisTotal,invoiceStatus:invoiceStatus,invoice_id:invoice_id,due_date:due_date,objet_name:objet_name,located_txt:located_txt},
                success:function(data){
                    // alert(data);
                    var json = JSON.parse(data);
                    var status = json.status;

                    if(status == 'success'){
                        location.href='invoice-list.php?sc=sucupd';
                    }
                }
            });

        }
    });

    //Invoice delete

    var invoice_deleted_id, invoice_deleted_row;
    $(document).on('click','.deleteInvoiceBtn',function(event){
        $("#deleteInvoiceModal").modal('show');
        invoice_deleted_id = $(this).data('id');
        invoice_deleted_row = $(this);
    });
     
    $(document).on('click','.deleteInvoiceModalBtn',function(){
        $.ajax({
            url:'invoice-delete.php',
            data:{id:invoice_deleted_id},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $(invoice_deleted_row).closest('tr').remove();
                    $("#deleteInvoiceModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });


    $(document).on("click","#addPaymentP",function(){
        lunchLoader();
    });

    //invoice payment by client

    $(window).on('load',function(){
        $('#paymentByClientModal').modal('show');
    });

    /**
     * modal Filter with broker or client click
    */

    $(document).on('click','#btnFilterWith',function(){
        if($(".broker-select-container").css("display") == "none"){
            $(".broker-select-container").show();
            $(".client-select-container").hide();
            $(this).text('Filtrer avec le Maître d\'ouvrage');
        }else{
            $(".client-select-container").show();
            $(".broker-select-container").hide();
            $(this).text('Filtrer avec l\'intermédiaire');
        }
    });





    //payment by client table


    $(document).on('click','.btnChangePaymentClient',function(){
        $('#paymentByClientModal').modal('show');
        $('#clientId').val("");
        $("#paymentByClientTable tbody tr").remove();
        $("#labelClientPaymentTotal").text(`0.00 DH`);
    });
    //btn search modal click


    $(document).on("click","#searchClientPayment",function(e){

        $("#selectClientModal").removeClass('border-danger');
        $("#selectBrokerModal").removeClass('border-danger');

        //
        

        if($(".broker-select-container").css("display") == "none" && $("#selectClientModal").val() == null){
            $("#selectClientModal").addClass('border-danger');
            return false;
        }else if($(".client-select-container").css("display") == "none" && $("#selectBrokerModal").val() == null){
            $("#selectBrokerModal").addClass('border-danger');
            return false;
        }
        


        e.preventDefault();
        var clientId = $("#selectClientModal").val();
        if(clientId != null){
            lunchLoader();
            $('#filter_type').val('client');
            $.ajax({
                url:"devisPayDetailsByClt.php",
                type:"POST",
                data:{clientId:clientId},
                success:function(data){
                    var json = JSON.parse(data)["data"];
                    console.log(json);
                    var html =``;
                    var total=0,
                    solde = 0;
                    if(json.length != 0){

                        json.forEach(row => {
                            html += `<tr>`;
                            // html += `<td>${row[0]}${row[8]}</td>`;
                            html += `<td>${row[0]}</td>`;
                            html += `<td>${row[1]}</td>`;
                            html += `<td>${row[2]}</td>`;
                            html += `<td>${row[3]}</td>`;
                            html += `<td>${row[4]}</td>`;
                            html += `<td class="totalRow">${row[5]} DH</td>`;
                            html += `<td class="soldeRow">${row[6]} DH</td>`;
                            html += `<td class="text-center">${row[7]}</td>`;
                            html += `</tr>`;
                            total += parseFloat(row[5]);
                            solde += parseFloat(row[6]);
                        });
                    }else{
                        html += `<tr><td colspan="7" class="text-center"><strong>No Data Available</strong></td></tr>`;
                    }

                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#paymentByClientTable tbody").html(html);
                    $('#clientId').val(clientId);
                    $("#labelClientPaymentTotal").text(`${(total - solde).toFixed(2)} DH`);
                    $("#hiddenTotal").val((total - solde).toFixed(2));
                    $("#hiddenTotalValue").val($("#labelClientPaymentTotal").text().split(' ')[0]);
                    $('#paymentByClientModal').modal('hide');

                    //Reset client and broker select to initiale value
                    $("#selectClientModal").val($("#selectClientModal option:first").val());
                    $("#selectBrokerModal").val($("#selectClientModal option:first").val());
                }
            });
        }
        var broker_id = $("#selectBrokerModal").val();
        if(broker_id != null){
            lunchLoader();
            $('#filter_type').val('broker');
            $.ajax({
                url:"devisPayDetailsByBrk.php",
                type:"POST",
                data:{broker_id:broker_id},
                success:function(data){
                    var json = JSON.parse(data)["data"];
                    var html =``;
                    var total=0,
                    solde = 0;
                    if(json.length != 0){

                        json.forEach(row => {
                            html += `<tr>`;
                            // html += `<td>${row[0]}${row[8]}</td>`;
                            html += `<td>${row[0]}</td>`;
                            html += `<td>${row[1]}</td>`;
                            html += `<td>${row[2]}</td>`;
                            html += `<td>${row[3]}</td>`;
                            html += `<td>${row[4]}</td>`;
                            html += `<td class="totalRow">${row[5]} DH</td>`;
                            html += `<td class="soldeRow">${row[6]} DH</td>`;
                            html += `<td class="text-center">${row[7]}</td>`;
                            html += `</tr>`;
                            total += parseFloat(row[5]);
                            solde += parseFloat(row[6]);
                        });
                    }else{
                        html += `<tr><td colspan="7" class="text-center"><strong>No Data Available</strong></td></tr>`;
                    }

                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#paymentByClientTable tbody").html(html);
                    // $('#clientId').val((json.length!=0)? json[0][8]:"");
                    $('#brokerId').val(broker_id);

                    $("#labelClientPaymentTotal").text(`${(total - solde).toFixed(2)} DH`);
                    $("#hiddenTotal").val((total - solde).toFixed(2));
                    $("#hiddenTotalValue").val($("#labelClientPaymentTotal").text().split(' ')[0]);
                    $('#paymentByClientModal').modal('hide');

                    //Reset client and broker select to initiale value
                    $("#selectClientModal").val($("#selectClientModal option:first").val());
                    $("#selectBrokerModal").val($("#selectClientModal option:first").val());
                }
            });
        }
        setTimeout(()=>$(".loader-wrapper").remove(),2000);
    });

    $(document).on('change','.CBPaymentByClient',function(e){
    //    $('.DevisCheckBox').click(); 
    // $(this).closest('td').children('.DevisCheckBox,.DossierCheckBox').click();
    $(this).closest('td').children('.DevisCheckBox, .DossierCheckBox').click();
    // $(this).closest('td').children('.DossierCheckBox').click();
    })



    // $(document).on("click","#searchClientPayment",function(e){
    //     e.preventDefault();
    //     var clientId = $("#selectClientModal").val();
    //     if(clientId != null){
    //         lunchLoader();
    //         $.ajax({
    //             url:"invoicePaymentDetails.php",
    //             type:"POST",
    //             data:{clientId:clientId},
    //             success:function(data){
    //                 var json = JSON.parse(data)["data"];
    //                 var html =``;
    //                 var total=0,
    //                 solde = 0;
    //                 if(json.length != 0){

    //                     json.forEach(row => {
    //                         html += `<tr>`;
    //                         html += `<td>${row[0]}${row[7]}</td>`;
    //                         html += `<td>${row[1]}</td>`;
    //                         html += `<td>${row[2]}</td>`;
    //                         html += `<td>${row[3]}</td>`;
    //                         html += `<td class="totalRow">${row[4]} DH</td>`;
    //                         html += `<td class="soldeRow">${row[5]} DH</td>`;
    //                         html += `<td class="text-center">${row[6]}</td>`;
    //                         html += `</tr>`;
    //                         total += parseFloat(row[4]);
    //                         solde += parseFloat(row[5]);
    //                     });
    //                 }else{
    //                     html += `<tr><td colspan="7" class="text-center"><strong>No Data Available</strong></td></tr>`;
    //                 }

    //                 $(".loader-wrapper").addClass("loader-hidden");
    //                 $("#paymentByClientTable tbody").html(html);
    //                 $('#clientId').val(clientId);
    //                 $("#labelClientPaymentTotal").text(`${(total - solde).toFixed(2)} DH`);
    //                 $("#hiddenTotal").val((total - solde).toFixed(2));
    //                 $("#hiddenTotalValue").val($("#labelClientPaymentTotal").text().split(' ')[0]);
    //                 $('#paymentByClientModal').modal('hide');
    //             }
    //         });
    //     }
    //     setTimeout(()=>$(".loader-wrapper").remove(),2000);
    // });

    $(document).on('click','.CBPaymentByClient',function(){
        checkPaymentCBS();
    });

    function checkPaymentCBS(){
        var total = 0,
        solde =0;
        var exist = false;
        $("#paymentByClientTable tbody tr").each(function(){
            
            if($(".CBPaymentByClient",this).is(':checked')){
                // $(".CBPaymentByClient",this).val();
                exist=true;
                total += parseFloat($(".totalRow",this).text().split(' ')[0]);
                solde += parseFloat($(".soldeRow",this).text().split(' ')[0]);
                return;
                
            }
        });
        if(exist){
            $("#labelClientPaymentTotal").text(`${(total - solde).toFixed(2)} DH`);
            $("#hiddenTotalValue").val($("#labelClientPaymentTotal").text().split(' ')[0]);
        }else{
            $("#labelClientPaymentTotal").text($("#hiddenTotal").val() + " DH");
            $("#hiddenTotalValue").val($("#labelClientPaymentTotal").text().split(' ')[0]);
        }
    }

    $(document).on("click",'.btnPayClientInvoice',function(){
        if($("#paymentClientPrice").val()==""){
            $("#paymentClientPrice").addClass("border border-danger");
            return false;
        }else{$("#paymentClientPrice").removeClass("border border-danger");}
        if($(".payment-method").val()==null){
            $(".payment-method").addClass("border border-danger");
            return false;
        }else{
            $(".payment-method").removeClass("border border-danger");
        }
    });

    $(document).on("click","#pay_inv",function(){
        if(
            $(".payment-method").val!=null &&
            $("#paymentClientPrice").val()!="" &&
            $("#payment_giver").val()!="" &&
            $(".CBPaymentByClient").length != 0)
        {
            lunchLoader();
        }
    });

    //payment info table 
    $('#paymentInfoTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        'order':[[4,'desc']],
        "pagingType": "input",
    });

    //purchase info table
    $('#purchaseTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
    });

    //purchase delete

    

    var purchase_deleted_id, purchase_deleted_row;
    $(document).on('click','.deletePurchaseBtn',function(event){
        $("#deletePurchaseModal").modal('show');
        purchase_deleted_id = $(this).data('id');
        purchase_deleted_row = $(this);
    });
    
    $(document).on('click','.deletePurchaseModalBtn',function(){
        $.ajax({
            url:'purchase-delete.php',
            data:{id:purchase_deleted_id},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $(purchase_deleted_row).closest('tr').remove();
                    $("#deletePurchaseModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });

    //loader
    function lunchLoader(){
        var wrapperDiv = document.createElement('div'),
        loaderDiv = document.createElement('div');
        wrapperDiv.classList.add("loader-wrapper");
        loaderDiv.classList.add('loader');
        // $(".loader-wrapper").append(loaderDiv);
        wrapperDiv.append(loaderDiv);
        $("body").append(wrapperDiv);
        
        // $(".loader-wrapper").addClass(".loader-hidden");
        // $(document).on("transitionend",".loader",function(){
        //     document.body.removeChild("loader-wrapper");
        // });
        removeLoader();
    }
    //remove loader
    function removeLoader(){
        setTimeout(function(){
            $(".loader-wrapper").addClass("loader-hidden")
            setTimeout(()=>$(".loader-wrapper").remove(),2000);
        },1000);
    }
    
    $(document).on("click","#add-user",function(){
        lunchLoader();
    });
    
    $(document).on("click","#pur_add",function(){
        if($("#serviceText").val()!="" && $("#purchasePrice").val()!="" && $("#purchaseNote").val()!=""){
            lunchLoader();
        }
    });



    //style tables pagination
    function stylePagination(){
        $(".paginate_button").attr("id","test");
        $(".previous").html('<i class="bi bi-caret-left"></i>');
        $(".next").html('<i class="bi bi-caret-right"></i>');
        $(document).on("click",".paginate_button",function(){
            $(".paginate_button").attr("id","test");
            $(".previous").html('<i class="bi bi-caret-left"></i>');
            $(".next").html('<i class="bi bi-caret-right"></i>');
        });
        
        $(".dataTables_paginate").addClass("w-100 text-center my-3");
    }
    
    
    $(document).on("click",".sorting",function(){
        setTimeout(function(){
            $(".paginate_button").attr("id","test");
            $(".previous").html('<i class="bi bi-caret-left"></i>');
            $(".next").html('<i class="bi bi-caret-right"></i>');
        },3)
    });
    $(document).on("change",".dataTables_length select",function(){
        setTimeout(function(){
            $(".paginate_button").attr("id","test");
            $(".previous").html('<i class="bi bi-caret-left"></i>');
            $(".next").html('<i class="bi bi-caret-right"></i>');
        },3)
    });
    $(document).on("input",".dataTables_filter input[type='search']",function(){
        setTimeout(function(){
            $(".paginate_button").attr("id","test");
            $(".previous").html('<i class="bi bi-caret-left"></i>');
            $(".next").html('<i class="bi bi-caret-right"></i>');
        })
    });
    // setInterval(function(){   
    //     stylePagination();
    //     console.log("az");
    // });

    //    if(!$("#test").length){

    //        setInterval(function(){
    //             stylePagination();
    //             console.log("az");
    //        },10);
    //    }

    // setTimeout(function(){
    //     stylePagination();
    // },500)
    
    // if($(".dataTables_paginate").length){
    // }
        
    /**
     * History Tables
    */
    //Client His Table
    $('#cusHistoryTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        'order':[[3,'desc']],
        "pagingType": "input",
    });
    
    //service His Table
    $('#serviceHistoryTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        'order':[[3,'desc']],
        "pagingType": "input",
    });
    
    //devis His Table
    $('#devisHistoryTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        'order':[[3,'desc']],
        "pagingType": "input",
    });

    //invoice His Table
    $('#invoiceHistoryTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        'order':[[3,'desc']],
        "pagingType": "input",
    });
    //purchase His Table
    $('#purchaseHistoryTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        'order':[[3,'desc']],
        "pagingType": "input",
    });


    $(document).on("click",".clearFilter",function(){
        $('#situationSelect').trigger('change');
    });

    let globalServices = [];
    //situation onchange event
    $(document).on("change","#situationSelect",function(){
        $("#situationTable tbody tr").remove();
        $("#expSitBtn a").remove();
        const clientID = $("#situationSelect").val();
        lunchLoader();
        if(clientID != null || clientID != ""){
            $.ajax({
                url:"st_info.php",
                data : {clientID:clientID},
                type:"POST",
                success:function(data){
                    
                    var json = JSON.parse(data)["data"];
                    var html =``;
                    let services = [];
                    let options = ``;
                    if(json.length != 0){

                        json.forEach(row => {
                            let price = row[8] == '0'?parseFloat(row[4]) * 1.2 : parseFloat(row[4]);

                        //   var status = row[6] == '1' ? '<span class="badge text-bg-success">Payé</span>' :
                        //     row[6] == '2' ? '<span class="badge avance-color">Avance</span>' :
                        //     row[6] == '0' ? '<span class="badge bg-danger">Non Payé</span>' :
                        //     '';
                        var status;
                        if(price.toFixed(2)==row[5]){
                            status= '<span class="badge text-bg-success">Payé</span>'
                        }else if(price.toFixed(2) !=row[5] && row[5]!=0.00){
                             status='<span class="badge avance-color">Avance</span>';
                        }else if(row[5]==0.00){ 
                             status='<span class="badge bg-danger">Non Payé</span>';
                        }

                            html += `<tr>`;
                            html += `<td>${row[0]}</td>`;
                            html += `<td>${row[1]}</td>`;
                            html += `<td>${row[2]}</td>`;
                            html += `<td>${row[3]}</td>`;
                            html += `<td>${price.toFixed(2)} DH</td>`;
                            html += `<td>${row[5]} DH</td>`;
                            html += `<td>${status} </td>`;
                            html += `<td class="text-center">${row[7]}</td>`;
                            html += `</tr>`;
                            services.push(row[3]);
                        });
                        let uniqueSrv = [...new Set(services)];
                        uniqueSrv.forEach(srv => {
                            // li += `<li>
                            //             <a class="dropdown-item" href="#">${srv}</a>
                            //         </li>`
                                    
                            options += `<option value="${srv}">${srv}</option>`;
                        });
                        $("#expSitBtn").html(`
                            <button class="btn border-0 "><i class="bi bi-x-circle fs-5 clearFilter"></i></button>
                            <select name="" id="" class="form-select srvFilter">
                                <option value="" selected disabled></option>
                                ${options}
                            </select>
                            <select name="" id="" class="form-select mx-2 statusFilter">
                                <option value="" selected disabled></option>
                                <option value="0">Non Payé</option>
                                <option value="1">Payé</option>
                                
                               
                            </select>
                            <div class="btn-group BtnExportSt" role="group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Export
                                </button>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item st_exp_pdf" target="_blank" href='situation_export.php?cl_id=${clientID}'>PDF</a></li>
                                <li><a class="dropdown-item st_exp_excel" target="_blank" href='situation_excel_export.php?cl_id=${clientID}'>Excel</a></li>
                                </ul>
                            </div>
                        `);
                        globalServices = services;
                    }else{
                        html += `<tr><td colspan="8" class="text-center"><strong>No Data Available</strong></td></tr>`;
                        $("#expSitBtn").html('');
                    }
                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#situationTable tbody").html(html);
                    // $("#expSitBtn").html(`<a target="_blank" href='situation_export.php?cl_id=${clientID}' class="btn btn-primary float-end" title="Imprimer Facture"><i class="bi bi-download "></i> Export</a>`);
                    
                }
            });
        }
        //add this on success 
        setTimeout(()=>$(".loader-wrapper").remove(),2000);
    });


    /**
    * Filter Situation with Payment status
    */

    $(document).on('change','.statusFilter',function(){
        const clientID = $("#situationSelect").val();
        const paid_status = $(this).val();
        console.log('statu'+paid_status);
        let srv_name = "";
        let st_pdf_href = `situation_export.php?cl_id=${clientID}`;
        let st_excel_href = `situation_excel_export.php?cl_id=${clientID}`;
        function makeRequest(){
            lunchLoader();
            if($(".srvFilter").val() != null){
                srv_name = $(".srvFilter").val();
                // hrefs for pdf && excel
                st_pdf_href = `situation_export.php?cl_id=${clientID}&pd_st=${paid_status}&srv_name=${srv_name.replaceAll(' ',"%20")}`;
                st_excel_href = `situation_excel_export.php?cl_id=${clientID}&pd_st=${paid_status}&srv_name=${srv_name.replaceAll(' ',"%20")}`;
                return $.ajax({
                    url:'st_info_both.php',
                    type:'POST',
                    data:{clientID:clientID,paid_status:paid_status,srv_name:srv_name}
                });
            }else{
                st_pdf_href = `situation_export.php?cl_id=${clientID}&pd_st=${paid_status}`;
                st_excel_href = `situation_excel_export.php?cl_id=${clientID}&pd_st=${paid_status}`;
                return $.ajax({
                    url:'st_info_status.php',
                    type:'POST',
                    data:{clientID:clientID,paid_status:paid_status}
                });
            }
        }
        $.when(makeRequest()).then(function successHandler(data){
            
            var json = JSON.parse(data)["data"];
                    var html =``;
                    let services = globalServices;
                    let options = ``;
                    if(json.length != 0){

                        json.forEach(row => {
                           
                            let price = row[8] == '0'?parseFloat(row[4]) * 1.2 : parseFloat(row[4]);
                            var status;
                            if(price.toFixed(2)==row[5]){
                                status= '<span class="badge text-bg-success">Payé</span>'
                            }else if(price.toFixed(2) !=row[5] && row[5]!=0.00){
                                 status='<span class="badge avance-color">Avance</span>';
                            }else if(row[5]==0.00){ 
                                 status='<span class="badge bg-danger">Non Payé</span>';
                            }
                            html += `<tr>`;
                            html += `<td>${row[0]}</td>`;
                            html += `<td>${row[1]}</td>`;
                            html += `<td>${row[2]}</td>`;
                            html += `<td>${row[3]}</td>`;
                            html += `<td>${price.toFixed(2)} DH</td>`;
                            html += `<td>${row[5]} DH</td>`;
                            html += `<td>${status}</td>`;
                            html += `<td class="text-center">${row[7]}</td>`;
                            html += `</tr>`;
                            // services.push(row[3]);
                        });
                        let uniqueSrv = [...new Set(services)];
                        uniqueSrv.forEach(srv => {
                            // li += `<li>
                            //             <a class="dropdown-item" href="#">${srv}</a>
                            //         </li>`;
                            options += `<option value="${srv}">${srv}</option>`;
                        });
                        $("#expSitBtn").html(`
                            <button class="btn border-0"><i class="bi bi-x-circle fs-5 clearFilter"></i></button>
                            <select name="" id="" class="form-select srvFilter">
                                <option value="" selected disabled></option>
                                ${options}
                            </select>
                            <select name="" id="" class="form-select mx-2 statusFilter">
                                <option value="" selected disabled></option>
                                <option value="0">Non Payé</option>
                                <option value="1">Payé</option>
                               
                              
                            </select>
                            <div class="btn-group BtnExportSt" role="group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Export
                                </button>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item st_exp_pdf" target="_blank" href=${st_pdf_href}>PDF</a></li>
                                <li><a class="dropdown-item st_exp_excel" target="_blank" href=${st_excel_href}>Excel</a></li>
                                </ul>
                            </div>
                        `);
                        $(".statusFilter").val(paid_status);
                        $(".srvFilter").val(srv_name);
                        $('.BtnExportSt').removeClass('invisible');
                    }else{
                        html += `<tr><td colspan="8" class="text-center"><strong>No Data Available</strong></td></tr>`;
                        // $("#expSitBtn").html('');
                        $('.BtnExportSt').addClass('invisible');
                    }
                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#situationTable tbody").html(html);
                    setTimeout(()=>$(".loader-wrapper").remove(),2000);
        }),
        function errorHandler(){
            console.log('error occurred');
        }
    });


    $(document).on('change','.srvFilter',function(){
        const clientID = $("#situationSelect").val();
        const srv_name = $(this).val();
        let paid_status = "";
        let st_pdf_href = `situation_export.php?cl_id=${clientID}`;
        let st_excel_href = `situation_excel_export.php?cl_id=${clientID}`;
        function makeRequest(){
            lunchLoader();
            if($(".statusFilter").val() != null){
                
                paid_status = $(".statusFilter").val();
                st_pdf_href = `situation_export.php?cl_id=${clientID}&pd_st=${paid_status}&srv_name=${srv_name.replaceAll(' ',"%20")}`;
                st_excel_href = `situation_excel_export.php?cl_id=${clientID}&pd_st=${paid_status}&srv_name=${srv_name.replaceAll(' ',"%20")}`;
                return $.ajax({
                    url:'st_info_both.php',
                    type:'POST',
                    data:{clientID:clientID,paid_status:paid_status,srv_name:srv_name}
                });
            }else{
                st_pdf_href = `situation_export.php?cl_id=${clientID}&srv_name=${srv_name.replaceAll(' ',"%20")}`;
                st_excel_href = `situation_excel_export.php?cl_id=${clientID}&srv_name=${srv_name.replaceAll(' ',"%20")}`;
                return $.ajax({
                    url:'st_info_srv.php',
                    type:'POST',
                    data:{clientID:clientID,srv_name:srv_name}
                });
            }
        }
        $.when(makeRequest()).then(function successHandler(data){
            
            var json = JSON.parse(data)["data"];
                    var html =``;
                    let services = globalServices;
                    let options = ``;
                    if(json.length != 0){

                        json.forEach(row => {
                            var status =  row[6]=='1'? '<span class="badge text-bg-success">Payé</span>' : '<span class="badge avance-color">Avance</span>' ;
                            let price = row[8] == '0'?parseFloat(row[4]) * 1.2 : parseFloat(row[4]);
                            html += `<tr>`;
                            html += `<td>${row[0]}</td>`;
                            html += `<td>${row[1]}</td>`;
                            html += `<td>${row[2]}</td>`;
                            html += `<td>${row[3]}</td>`;
                            html += `<td>${price.toFixed(2)} DH</td>`;
                            html += `<td>${row[5]} DH</td>`;
                            html += `<td>${status}</td>`;
                            html += `<td class="text-center">${row[7]}</td>`;
                            html += `</tr>`;
                            // services.push(row[3]);
                        });
                        let uniqueSrv = [...new Set(services)];
                        uniqueSrv.forEach(srv => {
                            // li += `<li>
                            //             <a class="dropdown-item" href="#">${srv}</a>
                            //         </li>`;
                            options += `<option value="${srv}">${srv}</option>`;
                        });
                        $("#expSitBtn").html(`
                            <button class="btn border-0"><i class="bi bi-x-circle fs-5 clearFilter"></i></button>
                            <select name="" id="" class="form-select srvFilter">
                                <option value="" selected disabled></option>
                                ${options}
                            </select>
                            <select name="" id="" class="form-select mx-2 statusFilter">
                                <option value="" selected disabled></option>
                                <option value="0">Non Payé</option>
                                <option value="1">Payé</option>
                                <option value="2">Avance</option>
                            </select>
                            <div class="btn-group BtnExportSt" role="group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Export
                                </button>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item st_exp_pdf" target="_blank" href=${st_pdf_href}>PDF</a></li>
                                <li><a class="dropdown-item st_exp_excel" target="_blank" href=${st_excel_href}>Excel</a></li>
                                </ul>
                            </div>
                        `);
                        $(".srvFilter").val(srv_name);
                        $(".statusFilter").val(paid_status);
                        $('.BtnExportSt').removeClass('invisible');
                    }else{
                        html += `<tr><td colspan="8" class="text-center"><strong>No Data Available</strong></td></tr>`;
                        // $("#expSitBtn").html('');
                        $('.BtnExportSt').addClass('invisible');
                    }
                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#situationTable tbody").html(html);
                    setTimeout(()=>$(".loader-wrapper").remove(),2000);
        }),
        function errorHandler(){
            console.log('error occurred');
        }
    });


    /**
     * dashboard card events
     */
    //dash function
    // function countDashVals(timeRange,eleDashTxt,elePeriodTxt,currency=''){
    //     const period = timeRange;
    //     lunchLoader();
    //     $.ajax({
    //         // url:url,
    //         url:"dash_sl_T.php",
    //         data:{period:period},
    //         type:"POST",
    //         success:function(data){
    //             $(`#${eleDashTxt}`).html(`${parseFloat(data).toLocaleString("fr-FR")} ${currency}`);
    //             $(`#${elePeriodTxt}`).html(`| This ${timeRange}`);
    //             $(".loader-wrapper").addClass("loader-hidden");
    //         }
    //     });
    //     setTimeout(()=>$(".loader-wrapper").remove(),2000);
    // }
    function countDashVals(timeRange){
        lunchLoader();
        $.ajax({
            // url:url,
            url:"dash_sl_T.php",
            data:{period:timeRange},
            type:"POST",
            success:function(data){
                let json = JSON.parse(data);
                //revenue
                $("#revenueDashTxt").html(`${parseFloat(json.revenue).toLocaleString("fr-FR")} DH`)
                //client
                $("#cusDashTxt").html(json.clients);
                //sales
                $("#salesDashTxt").html(json.sales);


                // $(`#revenuePeriodTxt,#cusDashTxt,#salesDashTxt`).html(`| This ${timeRange[0]} - ${timeRange[1]}`);
                // $(`#${eleDashTxt}`).html(`${parseFloat(data).toLocaleString("fr-FR")} ${currency}`);
                $(".loader-wrapper").addClass("loader-hidden");
            }
        });
        setTimeout(()=>$(".loader-wrapper").remove(),2000);
    }

    //client card
    //week btn
    // $(document).on('click','#btn-client-week',function(){
    //     countDashVals("Week",'dash_cl_T.php',`cusDashTxt`,'cusPeriodTxt');
    // });
    // //Month Btn
    // $(document).on('click','#btn-client-month',function(){
    //     countDashVals("Month",'dash_cl_T.php',`cusDashTxt`,'cusPeriodTxt');
    // });
    // //Year Btn
    // $(document).on('click','#btn-client-year',function(){
    //     countDashVals("Year",'dash_cl_T.php',`cusDashTxt`,'cusPeriodTxt');
    // });

    // //Sales Card
    
    // $(document).on('click','#btn-sales-week',function(){
    //     countDashVals("Week",'dash_sl_T.php',`salesDashTxt`,'salesPeriodTxt');
    // });
    // //Month Btn
    // $(document).on('click','#btn-sales-month',function(){
    //     countDashVals("Month",'dash_sl_T.php',`salesDashTxt`,'salesPeriodTxt');
    // });
    // //Year Btn
    // $(document).on('click','#btn-sales-year',function(){
    //     countDashVals("Year",'dash_sl_T.php',`salesDashTxt`,'salesPeriodTxt');
    // });

    // //Revenue Card

    // $(document).on('click','#btn-revenue-week',function(){
    //     countDashVals("Week",'dash_rv_T.php',`revenueDashTxt`,'revenuePeriodTxt','DH');
    // });
    // //Month Btn
    // $(document).on('click','#btn-revenue-month',function(){
    //     countDashVals("Month",'dash_rv_T.php',`revenueDashTxt`,'revenuePeriodTxt','DH');
    // });
    // //Year Btn
    // $(document).on('click','#btn-revenue-year',function(){
    //     countDashVals("Year",'dash_rv_T.php',`revenueDashTxt`,'revenuePeriodTxt','DH');
    // });

    // date range picker js
    //set the start and end Date
    var start = moment().subtract(29, 'days');
    var end = moment();
    //set function to initialize date value in the span
    function cb(start, end){
        $("#reportrange span").html(start.format("D/M/YYYY") + ' - ' + end.format("D/M/YYYY"));
        $("#reportrange").trigger("change");
    }
    //initializing date range picker
    $('#reportrange').daterangepicker({
        opens:"left",
        startDate:start,
        endDate:end,
        ranges:{
            'Aujourd\'hui':[moment(),moment()],
            'Hier':[moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Les 7 derniers jours':[moment().subtract(6, 'days'), moment()],
            'Les 30 derniers jours': [moment().subtract(29, 'days'), moment()],
            'Ce mois': [moment().startOf('month'), moment().endOf('month')],
            'Le mois dernier': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    },cb);

    cb(start, end);

    //on date range picker apply click
    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        const start = picker.startDate.format('YYYY-M-D');
        const end = picker.endDate.format('YYYY-M-D');
        const period = [start,end];
        countDashVals(period);
      });

    /**
     * broker Section
    * */
    //function to check Tel inputs
    function checkNumberInput(element){
        if(element.val().length>15){
            element.addClass('border-error');
            return true;
        }else{
            element.removeClass('border-error');
        }
    }

    var submitBrokerForm;
    $(document).on("input","#brokerTel",function(){
        submitBrokerForm = checkNumberInput($(this));
    });

    $(document).on("submit","#createBrokerForm",function(){
        if(submitBrokerForm){
            return false;
        }
    });

    $(document).on("click","#broker_add",function(){
        if($("#brokerNom").val()!="" && $("#brokerPrenom").val()!=""  && $("#brokerTel").val()!="" && $("#brokerAdr").val()!="" && !submitBrokerForm){
            lunchLoader();
        }
    });

    /**
     * broker Partie
    */

    $('#brokersTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
        'createdRow':function(row,data,dataIndex){
                $(row).attr('id',data[0]);
        },
    });

    $(document).on('click','.editBrokerBtn',function(){
        var id = $(this).data('id');
        var tr_id = $(this).closest('tr').attr('id');
        $.ajax({
            url:'get_slcd_Brk.php',
            data:{id:id},
            type:"POST",
            success:function(data){
                var json = JSON.parse(data);
                $("#id").val(json.id);
                $("#tr_id").val(tr_id);
                $("#brkNom").val(json.nom);
                $("#brkPrenom").val(json.prenom);
                $("#brkPhone").val(json.phone);
                $("#brkAdr").val(json.address);
                $("#brkSold").val(json.sold);
                $("#editBrokerModal").modal('show');
            }
        });
        
    });

    /**
     * update button click for broker Form
     */
    var submitEditBrkForm;
    $(document).on("input","#brkPhone",function(){
        submitEditBrkForm = checkNumberInput($(this));
    });

    $(document).on('submit','#editBrokerForm',function(){
        
        var id = $("#id").val()
        ,tr_id = $("#tr_id").val()
        ,brkNom = $("#brkNom").val()
        ,brkPrenom = $("#brkPrenom").val()
        ,brkPhone = $("#brkPhone").val()
        ,brkAdr = $("#brkAdr").val()
        ,brkSold = $("#brkSold").val();


        if(submitEditBrkForm){
            return false;
        }
        lunchLoader();
        $.ajax({
            url:"broker-edit.php",
            data:{id:id,brkNom:brkNom,brkPrenom:brkPrenom,brkPhone:brkPhone,brkAdr:brkAdr},
            type:'post',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    var table = $("#brokersTable").DataTable();
                    var button = '<a href="javascript:void(0);" data-id="'+id+'" class="btn btn-primary btn-sm editBrokerBtn" ><span><i class="bi bi-pencil-square"></i></span></a> <a href = "javascript:void(0);" data-id="'+id+'" class=" btn btn-danger btn-sm deleteBrokerBtn"><span><i class="bi bi-trash"></i></span></a>';
                    var row = table.row("[id='"+tr_id+"']");
                    row.row("[id='"+tr_id+"']").data([tr_id,brkNom,brkPrenom,brkPhone,brkAdr,brkSold,button]);
                    // cleaning inputs
                    $("#brkNom").val();
                    $("#brkPrenom").val();
                    $("#brkPhone").val();
                    $("#brkAdr").val();
                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#editBrokerModal").modal('hide');
                }else{
                    alert('Failed: connecting with Database error');
                }
            },
        });
    });

    /**
     * delete button click for broker Form
     */
    var broker_deleted_id, broker_deleted_row_id;
    $(document).on('click','.deleteBrokerBtn',function(event){
         $("#deleteBrokerModal").modal('show');
         broker_deleted_id = $(this).data('id');
         broker_deleted_row_id = $(this).parent().closest('tr').attr("id");
    });
     
    $(document).on('click','.deleteBrokerModalBtn',function(){
        $.ajax({
            url:'brk-del.php',
            data:{id:broker_deleted_id},
            type:'POST',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $('#brokersTable #'+broker_deleted_row_id).closest('tr').remove();
                    $("#deleteBrokerModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });
    
    /**
      * END fetching data to Broker table
    */

    $('#brokerHistoryTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input"
    });

    //devis select broker table
    $('#devisBrkTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input"
    });

    //devis Broker button click
    $(document).on('click',".devisAddBrkBtn",function(){
        $("#devisBrokerModal").modal("show");
        
       
    });


    //Dossier onchange event
    $(document).on("change","#dossierClientSelect",function(){
        $("#dossierClTable tbody tr").remove();
        const clientID = $("#dossierClientSelect").val();
        lunchLoader();
        if(clientID != null || clientID != ""){
            $.ajax({
                url:"dv_info.php",
                data : {clientID:clientID},
                type:"POST",
                success:function(data){
                    var json = JSON.parse(data)["data"];
                    var html =``;
                    if(json.length != 0){

                        json.forEach(row => {
                            
                            html += `<tr data-id="${row[6]}">`;
                            html += `<td>${row[0]}</td>`;
                            html += `<td>${row[1]}</td>`;
                            html += `<td>${row[2]}</td>`;
                            html += `<td>${row[3]}</td>`;
                            html += `<td class="text-center">${row[4]}${row[5]}</td>`;
                            html += `</tr>`;
                        });
                    }else{
                        html += `<tr><td colspan="7" class="text-center"><strong>No Data Available</strong></td></tr>`;
                    }
                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#dossierClTable tbody").html(html);
                }
            });
        }
        //add this on success 
        setTimeout(()=>$(".loader-wrapper").remove(),2000);
    });

    //on dossier table tr double click    //when i double click on the row of the table 
    $(document).on("click","#dossierClTable tbody tr button",function(){
        
        const devisId = $(this).closest('tr').data('id');
        if(devisId != undefined || devisId != null){
            lunchLoader();

            $.ajax({
                url:"ds_srv.php",
                data : {devisId:devisId},
                type:"POST",
                success:function(data){
                    // alert(data);
                    var json = JSON.parse(data)["data"];
                    var html =``;
                    if(json.length != 0){
                     
                        json.forEach(row => {
                            
                            html += `<div class="list-group-item list-group-item-action d-flex justify-content-between"  data-bs-toggle="list" data-d_id="${row[3]}">
                                        <div>
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1 fw-bolder">${row[0]}</h5>
                                            </div>
                                            <small class="ps-2">${row[1]} DH</small>
                                        </div>
                                        <button class="dsServiceItem btn btn-primary my-2">${row[2]}</button>
                                    </div>`;
                        });
                    }else{
                        html += `<tr><td colspan="7" class="text-center"><strong>No Data Available</strong></td></tr>`;
                    }
                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#showDvSrvDs").modal("show");
                    $("#showDvSrvDs .list-group").html(html);
                }
            });
        }
        //add this on success
        setTimeout(()=>$(".loader-wrapper").remove(),2000);
    });

    // check if reference exist or not in dossier
    $(document).on("input",".refTxt",function (e) {
       var ref_dossier=$(this).val();
       
       $.ajax({
        url:"check_ref_dossier.php",
        data:{ref_dossier:ref_dossier},
        type:"POST",
        success:function(data){
           
          let  json = JSON.parse(data);
          if(json.status =='success'){
            $('.refTxt').removeClass('border border-success');
            $('.refTxt').addClass('border border-danger');
            // disabled btn
            $('#btn_createDs').attr('disabled',true);
            // show text
            $('#error-ref-exist').text('Cette référence existe déjà veuillez choisir une autre');
           
          }else{
            $('.refTxt').removeClass('border border-danger');
            $('.refTxt').addClass('border border-success');
            // btn not disabled
            $('#btn_createDs').attr('disabled',false);
            // remove text
            $('#error-ref-exist').text('');



          }
        },error:function(xhr,err){
            console.log(err);
        }
      });
    });

    $(document).on("click",".dsServiceItem",function(){
        const d_devis_id = $(this).closest('div').data('d_id');
        $("#dossierClientSelect").prop("disabled",true);
        if(d_devis_id != ""){
            $("#showDvSrvDs").modal("hide");
            lunchLoader();
            $.ajax({
                url:"ds_deets.php",
                data:{d_devis_id:d_devis_id},
                type:"POST",
                success:function(data){
                    let json = JSON.parse(data);
                    let html = ``;
                    if(json.length != 0){

                        html += `<form id="ds_detail_form" method="POST">`;
                        html += `<input type="hidden" id="srv_id" value="${json[4]}">`;
                        html += `<div class="mb-3">
                                    <label  class="form-label fw-semibold">N° Dossier</label>
                                    <div class="input-group ps-3">
                                        <span class="input-group-text" id="ds_ref">${json[0]}</span>
                                        <input type="text" class="form-control refTxt" placeholder="N° Dossier" aria-describedby="ds_ref" required>
                                    </div>
                                    <p id="error-ref-exist" class="text-danger fs-5 ms-3 mt-2"></p>

                                </div>`;
                        html += `<div class="mb-3">
                                    <label  class="form-label fw-semibold">Objet</label>
                                    <div class="ps-3">
                                        <textarea id="ds_objet" class="form-control" rows="1" placeholder="Objet" disabled>${json[1]}</textarea>
                                    </div>
                                </div>`;
                        html += `<div class="mb-3">
                                    <label  class="form-label fw-semibold">Sis à</label>
                                    <div class="ps-3">
                                        <textarea id="ds_sis" class="form-control" rows="1" placeholder="Sis à" disabled >${json[5]}</textarea>
                                    </div>
                                </div>`;
                        html += `<div class="mb-3">
                                    <label  class="form-label fw-semibold">Service:</label>
                                    <div class="ps-3">
                                        <input id="ds_srv" type="text" class="form-control" value="${json[2]}" disabled>
                                    </div>
                                </div>`;
                        html += `<div class="mb-3">
                                    <label  class="form-label fw-semibold">Prix:</label>
                                    <div class="ps-3">
                                        <input id="ds_price" type="text" class="form-control" value="${json[3]} DH" disabled>
                                    </div>
                                </div>`;
                        html += `</form>`;

                        $(".loader-wrapper").addClass("loader-hidden");
                        $(".ds_content").html(html);
                        $("#btn_createDs").removeClass("invisible");
                    }
                }
            })
            
        }
    });


    $(document).on("click","#btn_createDs",function(){
        const srv_id = $("#srv_id").val();
        let refTxt = $(".refTxt");
        if(refTxt.val() == ""){
            refTxt.addClass("border-danger");
            return false;
        }else{
            refTxt.removeClass("border-danger");
        }
        
        if(srv_id != ""){

            $.ajax({
                url:"srv_aprv.php",
                type:"POST",
                data:{"srv_id":srv_id,"n_dossier":refTxt.val()},
                success:function(data){
                    let json = JSON.parse(data);
                    const status = json.status;
                    if(status === "success"){
                        lunchLoader();
                        location.href = "dossier-view.php";
                        // location.reload();
                    }
                }
            });
        }
        $(".loader-wrapper").addClass("loader-hidden");
    });


    $('#dossierTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input"
    });

    //inhancing search in dossier detail table 
    var table = $('#dossierTable').DataTable();
    $('.dataTables_filter input').on('keyup', function() {
        var searchTerm = this.value,
        regex = '\"' + searchTerm + '\"';
        table.rows().search(regex).draw();
    })


    //here the code for broker filter


    $(document).on("change","#brokerSelect",function(){
        
        const brokerId = $("#brokerSelect").val();
        if(brokerId != null || brokerId != ""){
            lunchLoader();
            $.ajax({
                url:"brk_flt.php",
                data : {brokerId:brokerId},
                type:"POST",
                success:function(data){
                    var json = JSON.parse(data)["data"];
                    var html =``;
                    if(json.length != 0){
                        //initializing table
                        html += `<table id="dsBrokerTable" class="table table-hover table-bordered table-striped" style="width:100%">`;
                        html += `<thead><tr>`;
                        html += `<th><a href="#">N°</a></th>`;
                        html += `<th><a href="#">N°Devis</a></th>`;
                        html += `<th><a href="#">Objet</a></th>`;
                        html += `<th><a href="#">Service name</a></th>`;
                        html += `<th><a href="#">Date creation</a></th>`;
                        html += `<th><a href="#">Action</a></th>`;
                        html += `</thead></tr>`;
                        html += `<tbody>`;
                        json.forEach(row => {
                            html += `<tr>`;
                            html += `<td>${row[0]}</td>`;
                            html += `<td><a target="_blank" href="devis-show.php?id=${row[1]}&client_id=${row[2]}" title="Afficher Devis Detail">${row[3]}</a></td>`;
                            html += `<td>${row[4]}</td>`;
                            html += `<td>${row[5]}</td>`;
                            html += `<td>${row[7]}</td>`;
                            html += `<td class="text-center"><a href="dossier-show.php?s_id=${row[6]}" class="btn btn-secondary btn-sm" title="Afficher Dossier detail" ><span><i class="bi bi-eye"></i></span></a></td>`;
                            html += `</tr>`;
                        });
                    }else{
                        html += `<tr><td colspan="7" class="text-center"><strong>No Data Available</strong></td></tr>`;
                    }
                    $(".loader-wrapper").addClass("loader-hidden");
                    $(".dsTableContent").html(html);
                }
            });
        }
        //add this on success 
        setTimeout(()=>$(".loader-wrapper").remove(),2000);
    });
    //client
    $(document).on("change","#clientSelectid",function(){
        
        const clientid = $("#clientSelectid").val();
        if(clientid != null || clientid != ""){
            lunchLoader();
            $.ajax({
                url:"getallDossierByclient.php",
                data : {clientid:clientid},
                type:"POST",
                success:function(data){
                    // alert(data);
                    var json = JSON.parse(data)["data"];
                    var html =``;
                    if(json.length != 0){
                        //initializing table
                        html += `<table id="clientSelectid" class="table table-hover table-bordered table-striped" style="width:100%">`;
                        html += `<thead><tr>`;
                        html += `<th><a href="#">N°</a></th>`;
                        html += `<th><a href="#">N°Devis</a></th>`;
                        html += `<th><a href="#">Objet</a></th>`;
                        html += `<th><a href="#">Service name</a></th>`;
                        html += `<th><a href="#">Date creation</a></th>`;
                        html += `<th><a href="#">Action</a></th>`;
                        html += `</thead></tr>`;
                        html += `<tbody>`;
                        json.forEach(row => {
                            html += `<tr>`;
                            html += `<td>${row[0]}</td>`;
                            html += `<td><a target="_blank" href="devis-show.php?id=${row[1]}&client_id=${row[2]}" title="Afficher Devis Detail">${row[3]}</a></td>`;
                            html += `<td>${row[4]}</td>`;
                            html += `<td>${row[5]}</td>`;
                            html += `<td>${row[7]}</td>`;
                            html += `<td class="text-center"><a href="dossier-show.php?s_id=${row[6]}" class="btn btn-secondary btn-sm" title="Afficher Dossier detail" ><span><i class="bi bi-eye"></i></span></a></td>`;
                            html += `</tr>`;
                        });
                    }else{
                        html += `<tr><td colspan="7" class="text-center"><strong>No Data Available</strong></td></tr>`;
                    }
                    $(".loader-wrapper").addClass("loader-hidden");
                    $(".dsTableContent").html(html);
                    
                }
            });
        }
        //add this on success 
        setTimeout(()=>$(".loader-wrapper").remove(),2000);
    });
    
    $(document).on("click","#dsResetBtn",function(){
        const brokerId = $("#brokerSelect").val();
        if(brokerId != null ){
            location.reload();
        }
        const clientId = $("#clientSelectid").val();
        if(clientId != null ){
            location.reload();
        }
    });

    /**
     * Convert Devis to Facture
    **/

    $(document).on('click','.btnConvertToFacture',function(){
        let devis_id = $("#devis_id").val();
        // console.log(devis_id);
        
        function makeRequest(){
            lunchLoader();
            return $.ajax({
                url:'convertToFct.php',
                type:'POST',
                data:{devis_id:devis_id}
            });
        }
        $.when(makeRequest()).then(function successHandler(data){
            //success message........
            var json = JSON.parse(data);
            var status = json.status;
            if(status == 'success'){
                $(".loader-wrapper").addClass("loader-hidden");
                let id = json.invoice_id,
                client_id = json.client_id;
                location.href = `invoice-view.php?id=${id}&client_id=${client_id}`;
            }
            else if (status == 'emptyDevis') {
                $(".loader-wrapper").addClass("loader-hidden");
                setTimeout(()=>$(".loader-wrapper").remove(),2000);
                Swal.fire({
                    icon: 'warning',
                    title: 'Il n\'y a pas de services approuvés dans ce devis',
                })
            }
        }),
        function errorHandler(){
            console.log('error occurred');
        }
    })

    /**
     * Category Supplier Section
    */

    // $(document).on('click','#category_add',function(){
    //     $('#catSelectError').text('');
    //     $('#catTypeSelect').val();
    //     if($('#catTypeSelect').val() == null){
    //         $('#catSelectError').text('This Field is required');
    //         return false;
    //     }
    // });

    $('#suppCatTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
        'createdRow':function(row,data,dataIndex){
                $(row).attr('id',data[0]);
        },
    });

    $(document).on('click','.editSuppCatBtn',function(){
        var id = $(this).data('id');
        var tr_id = $(this).closest('tr').attr('id');
        $.ajax({
            url:'get_slcd_suppCat.php',
            data:{id:id},
            type:"POST",
            success:function(data){
                var json = JSON.parse(data);
                $("#id").val(json.id);
                $("#tr_id").val(tr_id);
                $("#catTitle").val(json.title);
                $("#catTypeSelect").val(json.type);
                $("#editSuppCatModal").modal('show');
            }
        });
        
    });

    /**
     * update button click for Category Form
     */

    $(document).on('submit','#editSuppCatForm',function(){
        
        var id = $("#id").val()
        ,tr_id = $("#tr_id").val()
        ,catTitle = $("#catTitle").val()
        ,catType = $("#catTypeSelect").val();


        lunchLoader();
        $.ajax({
            url:"suppCat-edit.php",
            data:{id:id,catTitle:catTitle,catType:catType},
            type:'POST',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    var table = $("#suppCatTable").DataTable();
                    var button = '<a href="javascript:void(0);" data-id="'+id+'" class="btn btn-primary btn-sm editSuppCatBtn" ><span><i class="bi bi-pencil-square"></i></span></a> <a href = "javascript:void(0);" data-id="'+id+'" class=" btn btn-danger btn-sm deleteSuppCatBtn"><span><i class="bi bi-trash"></i></span></a>';
                    var row = table.row("[id='"+tr_id+"']");
                    row.row("[id='"+tr_id+"']").data([tr_id,catTitle,catType,button]);
                    // cleaning inputs
                    $("#catTitle").val();
                    $("#catTypeSelect").val('');
                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#editSuppCatModal").modal('hide');
                }else{
                    alert('Failed: connecting with Database error');
                }
            },
        });
    });

    /**
     * delete button click for broker Form
     */
    var suppCat_deleted_id, suppCat_deleted_row_id;
    $(document).on('click','.deleteSuppCatBtn',function(event){
         $("#deleteSuppCatModal").modal('show');
         suppCat_deleted_id = $(this).data('id');
         suppCat_deleted_row_id = $(this).parent().closest('tr').attr("id");
    });
     
    $(document).on('click','.deleteSuppCatModalBtn',function(){
        $.ajax({
            url:'suppCat-del.php',
            data:{id:suppCat_deleted_id},
            type:'POST',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $('#suppCatTable #'+suppCat_deleted_row_id).closest('tr').remove();
                    $("#deleteSuppCatModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });

    /**
     * Supplier Section 
    */


    $('#supplierTable').DataTable({
        'info':false,
        'responsive':true,
        'processing':true,
        "pagingType": "input",
        'createdRow':function(row,data,dataIndex){
                $(row).attr('id',data[0]);
        },
    });

    $(document).on('click','.editSupplierBtn',function(){
        var id = $(this).data('id');
        var tr_id = $(this).closest('tr').attr('id');
        $.ajax({
            url:'get_slcd_supp.php',
            data:{id:id},
            type:"POST",
            success:function(data){
                var json = JSON.parse(data);
                $("#id").val(json.id);
                $("#tr_id").val(tr_id);
                $("#supplierFullName").val(json.full_name);
                $("#supplierAdr").val(json.address);
                $("#supplierPhone").val(json.phone);
                $("#suppCatSelect").val(json.cat_id);
                $("#editSupplierModal").modal('show');
            }
        });
        
    });

    /**
     * update button click for Supplier Edit Form
     */

    $(document).on('submit','#editSupplierForm',function(){
        
        var id = $("#id").val()
        ,tr_id = $("#tr_id").val()
        ,full_name = $("#supplierFullName").val()
        ,phone = $("#supplierPhone").val()
        ,address = $("#supplierAdr").val()
        ,cat_id = $("#suppCatSelect").val();

        lunchLoader();
        $.ajax({
            url:"supplier-edit.php",
            data:{id:id,full_name:full_name,phone:phone,address:address,cat_id:cat_id},
            type:'POST',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    var table = $("#supplierTable").DataTable();
                    var button = '<a href="javascript:void(0);" data-id="'+id+'" class="btn btn-primary btn-sm editSupplierBtn" ><span><i class="bi bi-pencil-square"></i></span></a> <a href = "javascript:void(0);" data-id="'+id+'" class=" btn btn-danger btn-sm deleteSupplierBtn"><span><i class="bi bi-trash"></i></span></a>';
                    var row = table.row("[id='"+tr_id+"']");
                    row.row("[id='"+tr_id+"']").data([tr_id,full_name,phone,address,json.category,json.sold,button]);
                    // cleaning inputs
                    $("#supplierFullName").val();
                    $("#supplierPhone").val('');
                    $("#supplierAdr").val('');
                    $("#suppCatSelect").val('');
                    $(".loader-wrapper").addClass("loader-hidden");
                    $("#editSupplierModal").modal('hide');
                }else{
                    alert('Failed: connecting with Database error');
                }
            },
        });
    });

    /**
     * delete button click for broker Form
     */
    var supplier_deleted_id, supplier_deleted_row_id;
    $(document).on('click','.deleteSupplierBtn',function(event){
        $("#deleteSupplierModal").modal('show');
        supplier_deleted_id = $(this).data('id');
        supplier_deleted_row_id = $(this).parent().closest('tr').attr("id");
    });
    
    $(document).on('click','.deleteSupplierModalBtn',function(){
        $.ajax({
            url:'supplier-del.php',
            data:{id:supplier_deleted_id},
            type:'POST',
            success:function(data){
                var json = JSON.parse(data);
                var status = json.status;
                if(status == 'success'){
                    $('#supplierTable #'+supplier_deleted_row_id).closest('tr').remove();
                    $("#deleteSupplierModal").modal('hide');
                }else{
                    alert('Failed: connection with Database error');
                }
            }
        });
    });

    /**
     * Supplier Payment 
    */

    $(document).on('change','#supplierCheckbox',function(){
        if($(this).is(':checked')){
            $('.supplierContainer').show('fast');
        }else{
            $('.supplierContainer').hide('fast');
        }
    });





    //this function need to be on last line
    stylePagination();
});
    
    
//js
    
function saveInvoicePayment(){
    var total = $("#labelDevisTotal").text().split(" ")[0];
    var invoice_payment = $("#invoice_payment").val();
    // console.log( parseFloat(total));

    if($(".invoice-prix-txt").val()!= "" &&  $(".select-pay-method").val()==null){
        $(".select-pay-method").addClass("border border-danger");
        return;
    }else{
        $(".select-pay-method").removeClass("border border-danger");
    }

    if($(".invoice-prix-txt").val()!= "" && $(".invoice-giver-txt").val()==""){$(".invoice-giver-txt").addClass("border border-danger");return;}
    else{$(".invoice-giver-txt").removeClass("border border-danger");}

    if($.isNumeric(total) && parseFloat(total) != 0 && $(".select-pay-method").val()!=null){
        var checkPayment = parseFloat(invoice_payment)>parseFloat(total)? parseFloat(total): parseFloat(invoice_payment);
        $("#labelInvoicePayment").text(parseFloat(checkPayment).toFixed(2)+' DH');
        $("#labelInvoiceSolde").text((parseFloat(total) - parseFloat(checkPayment)).toFixed(2) + ' DH');
        $(".btn-invoice-payment").click();
    }else{
        $("#invoice_payment").val("");
        $(".select-pay-method").val("");
        $(".invoice-giver-txt").val("");
        $("#labelInvoicePayment").text("0.00 DH");
        $("#labelInvoiceSolde").text("0.00 DH");
    }
}


function rowTotal(){
    // console.log($('.removeTva')[0]);
    var grand_total = 0,
    disc = 0;
    $('.servicesTable tbody tr').each(function(){
        var rowQte = $('.rowServiceQte',this).val(),
        rowPrice = $('.servicePrice',this).val(),
        rowDiscount = $('.serviceDiscount',this).val(),
        rowMontant = $('.rowServiceTotal',this);
        var discount = (rowDiscount == "") ? 0 : parseFloat(rowDiscount)/100;
        var originalPrice = parseFloat(rowQte)*parseFloat(rowPrice);
        var res = isNaN(originalPrice)? 0 : (originalPrice - (originalPrice*discount)).toFixed(2);
        rowMontant.val(res);

        grand_total += parseFloat(rowMontant.val());
        disc += originalPrice - res;

    });
    $(".labelSubTotal").text(grand_total.toFixed(2)+" DH");
    $(".labelDiscount").text(isNaN(disc) ? `${0} DH` : `${disc.toFixed(2)} DH`);

    var tva = 0.2;
    var price_Tva = (grand_total*tva) + grand_total;
    var addedTvaPrice = price_Tva - grand_total
    $('.labelTva').text(addedTvaPrice.toFixed(2)+" DH");
    $('.labelDevisTotal').text(price_Tva.toFixed(2)+" DH");

    if($('.removeTvaClient').is(':checked')){
        $('.labelTva').text('0.00 DH');
        $('.labelDevisTotal').text(grand_total.toFixed(2)+" DH");
    }
    saveInvoicePayment();
}

function brkRowTotal(){
    var grand_total = 0,
    disc = 0;
    // console.log( $('.devisShowTableBrk tbody tr'));
    $('.devisShowTableBrk tbody tr').each(function(){
        var rowQte = $('.rowBrkServiceQte',this).val(),
        rowPrice = $('.serviceBrkPrice',this).val(),
        rowDiscount = $('.serviceBrkDiscount',this).val(),
        rowMontant = $('.rowServiceBrkTotal',this);
        // console.log(rowQte);
        var discount = (rowDiscount == "") ? 0 : parseFloat(rowDiscount)/100;
        // alert('qte : '+rowQte);
        var originalPrice = parseFloat(rowQte)*parseFloat(rowPrice);
        var res = isNaN(originalPrice)? 0 : (originalPrice - (originalPrice*discount)).toFixed(2);
        rowMontant.val(res);


        grand_total += parseFloat(rowMontant.val());
        disc += originalPrice - res;

    });
    $(".labelBrkSubTotal").text(grand_total.toFixed(2)+" DH");
    $(".labelBrkDiscount").text(isNaN(disc) ? `${0} DH` : `${disc.toFixed(2)} DH`);

    var tva = 0.2;
    var price_Tva = (grand_total*tva) + grand_total;
    var addedTvaPrice = price_Tva - grand_total

    $('.labelBrkTva').text(addedTvaPrice.toFixed(2)+" DH");
    $('.labelBrkDevisTotal').text(price_Tva.toFixed(2)+" DH");

    if($('.removeTva').is(':checked')){
        $('.labelBrkTva').text('0.00 DH');
        $('.labelBrkDevisTotal').text(grand_total.toFixed(2)+" DH");
    }
    // saveInvoicePayment();
}
// brkRowTotal();