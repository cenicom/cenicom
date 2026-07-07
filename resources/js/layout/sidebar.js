const button = document.getElementById('sidebarToggle');

if(button){

    button.addEventListener('click',()=>{

        document.body.classList.toggle('sidebar-collapsed');

    });

}