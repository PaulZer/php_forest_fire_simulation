window.addEventListener("DOMContentLoaded", (event) => {

    const conn = new WebSocket('ws://localhost:8088');

    const connId = generateConnectionId();

    conn.onopen = function (e) {

        document.getElementById('simulation').innerHTML = (document.createElement('p').innerHTML = "Connection established ! Simulation will load...");

    };

    conn.onmessage = function (e) {
        console.log("Updated view !");
        let jsonData = JSON.parse(e.data);

        renderView(jsonData['html']);

        if(jsonData['status'] !== 'over') {

            setTimeout(() => {
                conn.send(JSON.stringify({
                    'connId': connId,
                    'action': 'iterate'
                }));
            }, "250");
        }
    };

    conn.onclose = function (e) {
        console.log("Connection closed !");
    }

    function renderView(html) {
        document.getElementById('simulation').innerHTML = html;

    }

    function generateConnectionId() {
        const dateString = Date.now().toString(36);
        const randomness = Math.random().toString(36).slice(2);
        return dateString + randomness;
    }

    function resetSimulation() {
        console.log("Resetting simulation ! ConnID = "+connId);
        conn.send(JSON.stringify({
            'connId': connId,
            'action': 'reset'
        }));
    }

    console.log(document.getElementById('reset'));

    document.getElementById('reset').addEventListener('click', resetSimulation);
});