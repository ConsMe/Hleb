function setHeader() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}


function refreshAppTokens() {
    $.get('/')
    .then((data) => {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = data;
        console.log(wrapper)
        return wrapper.querySelector('meta[name=csrf-token]').getAttribute('content');
    })
    .then((token) => {
        document.querySelector('meta[name=csrf-token]').setAttribute('content', token);
        setHeader();
        console.log('refreshed')
    })
}

setInterval(refreshAppTokens, 1000 * 60 * 60);
setHeader();