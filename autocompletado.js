$(function() {



    var availableTags = [
        "*ESPAÑOL",
        "*INGLES",
        "*FRANCES",
        "*ITALIANO",
        "*JAPONES"
    ];
    $("#idioma").autocomplete({
        source: availableTags
    });



    var availableTags = [
        "VENEZUELA",
        "ITALIA",
        "CHINA",
        "EEUU",
        "BRASIL",
        "COLOMBIA",
        "PERU"
    ];
    $("#pais").autocomplete({
        source: availableTags
    });


    var availableTags = [
        "*Normal",
        "*VIP 1",
        "*VIP 2",
        "*VIP 3",
        "*VIP 4",
        "*VIP 5"
    ];
    $("#vip").autocomplete({
        source: availableTags
    });


    var availableTags = [
        "*PaginaWeb",
        "*Telefono/Llamada",
        "*Walkin",
        "*Correo Electronico",
        "*Booking Web"
    ];
    $("#medioreservacion").autocomplete({
        source: availableTags
    });


    var availableTags = [
        "*Provincial",
        "*Banesco",
        "*Venezuela",
        "*Tesoro",
        "*BOD",
        "*Mercantil",
        "*Banco Nacional del Credito",
        "*Bicentenario",
        "*Banplus",
        "*Banco del Exterior",
        "*Banco del Caribe"
    ];
    $("#banco").autocomplete({
        source: availableTags
    });

    var availableTags = [
        "*INGENIERO(A)",
        "*LICENCIADO(A)",
        "*ABOGADO(A)",
        "*DOCTOR(A) ",
        "*PROFESOR(A)"
    ];
    $("#profesion").autocomplete({
        source: availableTags
    });


    var availableTags = [
        "*TDC",
        "*EFECTIVO",
        "*CHEQUE",
        "*OTRO"
    ];
    $("#tipogarantia").autocomplete({
        source: availableTags
    });






    var availableTags = [
        "*@gmail.com",
        "*@hotmail.com",
        "*@hotmail.es",
        "*@yahoo.com"
    ];
    $("#correo").autocomplete({
        source: availableTags
    });



});