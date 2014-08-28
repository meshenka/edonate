/* c style file 
for varnish 3.x
*/
backend default {
    .host = "donate.loc";
    .port = "80";
}

// annonce son support d'ESI
sub vcl_recv {
    set req.http.Surrogate-Capability = "abc=ESI/1.0";

	if (req.http.x-forwarded-for) {
        set req.http.X-Forwarded-For = req.http.X-Forwarded-For + ", " + client.ip;
    } else {
        set req.http.X-Forwarded-For = client.ip;
    }

	if (req.request == "PURGE") {
    
    	return (lookup);
	}
    //TODO toujours cacher les fichiers css js png gif jpg jpeg ttf woff ico 
}

/*
optimisez Varnish afin qu'il analyse uniquement le contenu de la 
réponse lorsqu'il y a au moins une balise ESI en vérifiant l'en-tête 
Surrogate-Control que Symfony2 ajoute automatiquement
*/
sub vcl_fetch {
    if (beresp.http.Surrogate-Control ~ "ESI/1.0") {
        unset beresp.http.Surrogate-Control;
        set beresp.do_esi = true;
    }

	if (beresp.ttl < 1s ) {
        set beresp.http.X-Cacheable = "NO:TTL-is-0s";
        return (hit_for_pass);
    }

	// on ne cache rien si il y a des cookies    
    if (beresp.http.Set-Cookie) {
    	
    	 set beresp.http.X-Cacheable = "NO:Set-Cookie";
        return (hit_for_pass);
    }
    
    set beresp.http.X-Cacheable = "YES";
    return (deliver);
}

sub vcl_deliver {
	if (obj.hits > 0) {
    	set resp.http.X-Cache = "HIT";
	} else {
    	set resp.http.X-Cache = "MISS";
	}
}
/*
accepter une méthode HTTP spécifique PURGE qui va invalider le cache pour une 
ressource donnée
*/
sub vcl_hit {
    if (req.request == "PURGE") {
        purge;
        error 200 "Purged";
    }
}

sub vcl_miss {
    if (req.request == "PURGE") {
    	purge;
        error 404 "Not purged";
    }
}

