var money = $("#money");
var water = $("#water");
var seeds = $("#seeds");
var harvest = $("#harvest");

var cheat = $("#cheat");
var bWater = $("#bWater");
var bSeeds = $("#bSeeds");
var sHarvest = $("#sHarvest");

function fieldClick(field_id, cell) {
    $.post("/actions/fieldClick.php", {field_id: field_id}, function (resAttr) {
        $("#answer").html(resAttr);
        var result = JSON.parse(resAttr);
        $(cell).text(result['symbol']);
        money.text(result['money']);
        water.text(result['water']);
        seeds.text(result['seeds']);
        harvest.text(result['harvest']);
    })
}

function store(operation){
    $.post("/actions/store.php", {operation: operation}, function (resAttr) {
        $("#answer").html(resAttr);
        var result = JSON.parse(resAttr);
        money.text(result['money']);
        water.text(result['water']);
        seeds.text(result['seeds']);
        harvest.text(result['harvest']);
    })
}

$(".table").on('click', '.field', function () {
    fieldClick($(this).data('id'), this);
})

cheat.click(function () {
    store("cheat");
})

bWater.click(function () {
    store("buyWater");
})

bSeeds.click(function () {
    store("buySeeds");
})

sHarvest.click(function () {
    store("sellHarvest");
})