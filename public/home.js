(() => {
    ['latest_runs', 'world_records'].forEach(table => {
        const oReq = new XMLHttpRequest();
        oReq.addEventListener('load', function () {
            document.getElementById(table).innerHTML = this.responseText;
        });
        oReq.open('GET', `/${table}`);
        oReq.send();
    });
})();
