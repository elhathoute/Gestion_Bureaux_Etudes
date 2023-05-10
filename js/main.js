(function(){
    "use strict";


    /**
     * Selector helper function
     */
    const select = (el,all=false)=>{
        el = el.trim();
        if(all){
            return [...document.querySelectorAll(el)];
        } else {
            return document.querySelector(el);
        }
    };

    /**
     * Event listener function
     */

    const on = (type, el, listener, all=false)=>{
        if(all){
            select(el,all).forEach(element => element.addEventListener(type,listener));
        }else{
            select(el,all).addEventListener(type,listener);
        }
    };

    /**
     * Sidebar toggle
     */
    if(select('.toggle-sidebar-btn')){
        on("click",".toggle-sidebar-btn",function(e){
            select("body").classList.toggle("toggle-sidebar");
        });
    }

    /**
     * Initiate Datatables
     */
    // const datatables = select('.dataTable', true)
    // datatables.forEach(datatable => {
    //     new simpleDatatables.DataTable(datatable);
    // })


})();

