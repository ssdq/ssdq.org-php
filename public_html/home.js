(() => {
    ['latest_runs', 'world_records'].forEach(table => {
        const oReq = new XMLHttpRequest();
        oReq.addEventListener('load', function () {
            document.getElementById(table).innerHTML = oReq.status === 200
                ? this.responseText
                : 'An error has occurred while attempting to load data from speedrun.com';
        });
        oReq.open('GET', `/${table}`);
        oReq.send();
    });
})();
