<form 
    method="GET" 
    onsubmit="event.preventDefault(); const code = this.lobby_code.value.trim(); if(code) window.location.href='/lobby/' + code + '/enter';"
    class="flex items-center space-x-2 bg-gray-100 px-2 py-1 rounded-md shadow-sm ml-6"
>
    <input
        type="text"
        name="lobby_code"
        placeholder="Lobby kód..."
        class="bg-transparent outline-none px-2 py-1 w-28 text-sm placeholder-gray-500"
        required
    >
    <button
        type="submit"
        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm"
    >JOIN</button>
</form> 