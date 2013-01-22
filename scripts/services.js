angular.module("nviteServices", ["ngResource"])
    .factory("Response", function ($resource) {
        return $resource("api/Response.php", {}, {
            add: { 
            	method: "POST", 
            	params: { 
                    client: "@client",
                    event: "@event",
	            	attending: "@attending", 
	            	firstName: "@firstName", 
	            	lastName: "@lastName", 
	            	phone: "@phone", 
	            	email: "@email", 
	            	guests: "@guests" 
            	} 
        	}
        });
    });