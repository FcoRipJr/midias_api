<style>
    input, select{
        width:80%;
    }
    label{
        width:20%;
    }
    textarea{
        width:100%;
        min-height: 350px;
    }
</style>
<label>url</label>
<input type="text"id="url">

<br>
<label>Metodo</label>
<select id="method">
    <option value="GET">GET</option>
    <option value="POST">POST</option>
    <option value="PUT">PUT</option>
    <option value="DELETE">DELETE</option>
</select>

<br>
<div style="display:flex;">
    <div style="width:50%;">
        <label>content <button onclick="send()">enviar</button></label> 
        <br>
        <textarea id="content"></textarea>

    </div>
    <div style="width:50%;">
        <label>output</label>
        <input type="text" id="response_code"></input>
        <br>

        <textarea id="output"></textarea>

    </div>
</div>
<br>



<script>
    function send(){
        document.getElementById('response_code').value = ''
        document.getElementById('output').value = ''
        let header = {
            method: document.getElementById('method').value,
            body: document.getElementById('content').value,
        }
        if(header.method == "GET"){
            delete header.body
        }
        fetch(document.getElementById('url').value, header )
        .then((response) => {
            document.getElementById('response_code').value = response.status
            return response.json()
        })
        .then((content) => {
            document.getElementById('output').value = JSON.stringify(content)
        })
    }
</script>