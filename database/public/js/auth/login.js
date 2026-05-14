function showPanel(panel, button){

    document.querySelectorAll('.panel')
    .forEach(item=>{
        item.classList.remove('active');
    });

    document.querySelectorAll('.tab')
    .forEach(item=>{
        item.classList.remove('active');
    });

    document.getElementById(panel + '-panel')
    .classList.add('active');

    button.classList.add('active');
}

function togglePassword(id){

    const input = document.getElementById(id);

    if(input.type === 'password'){
        input.type = 'text';
    }else{
        input.type = 'password';
    }
}