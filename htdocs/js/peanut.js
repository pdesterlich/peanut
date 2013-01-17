var peanut = {

	url: function(controller, action, id, params) {
		var result = "";

		// legge l'url base, se necessario toglie lo slash finale
		var urlBase = (configUrlBase.substring(configUrlBase.length-1) === "/") ? configUrlBase.substring(0, configUrlBase.length-2) : configUrlBase;
		// inizializza il risultato (protocollo + url base)
		if (urlBase !== "") {
			result = configUrlProtocol + "://" + urlBase;
		}

		if (configUrlShort) {
			if (controller) { result += "/" + controller; } // se definito, aggiungo il controller
			if (action) { result += "/" + action; } // se definito, aggiungo l'action
			if ((id) && (id !== '')) { result += "/" + id; } // se definito, aggiungo l'id
			if (params) {
				if (params.substring(0, 1) !== '?') {
					if (params.substring(0, 1) === '&') {
						params = '?' + params.substring(1);
					} else {
						params = '?' + params;
					}
				}
			}
			if (params) { result += params; } // se definito, aggiungo gli eventuali altri parametri
			if (result === "") { result = "/"; }
		} else {
			result += "index.php"; // genero l'url di base
			if (controller) { result += "?controller=" + controller; } // se definito, aggiungo il controller
			if (action) { result += "&action=" + action; } // se definito, aggiungo l'action
			if (id) { result += "&id=" + id; } // se definito, aggiungo l'id
			if (params) { result += params; } // se definito, aggiungo gli eventuali altri parametri
		}
		return result; // ritorno l'url calcolato
	}

};