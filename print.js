var settings = {
    "async": true,
    "crossDomain": true,
    "url": "./convert.php",
    "method": "GET"
    }


function currenciesList () {
    
    $.ajax(settings).done(
        function (response) {

            const list = document.querySelector('#list');
            currencies = JSON.parse(response);

            Object.keys(currencies).forEach(key => {

                if(document.querySelector('#'+key)) {

                    li = document.querySelector('#'+key);
                    li.innerHTML = key + ': ' + currencies[key];

                } else {

                    let li = document.createElement('li');
                    li.id = key;
                    li.textContent = key + ': ' + currencies[key];
                    list.appendChild(li);

                }

                console.log(key, currencies[key]);
            });

        }
    );

};

setInterval( function(){
    currenciesList();
}, 60000)