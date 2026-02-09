"use strict";

// Class definition

var KTKanbanBoardDemo = function() {
    // Private functions
    var _demo1 = function() {
        var kanban = new jKanban({
            element: '#kt_kanban_1',
            gutter: '0',
            widthBoard: '100%',
            // dropEl: function(el, target, source, sibling) {
            //     saveColumnPositions();
            // },
            boards: [{
                    'id': '_columna12',
                    'title': 'PRINCIPAL',
                    'class': '',
                    'item': [{
                            'id': 'item_' + Math.random().toString(36).substr(2, 9),  // Genera un ID único
                            'title': `<div class="card card-custom">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h3 class="card-label">Basic Card
                                                    <small>sub title</small></h3>
                                                </div>
                                            </div>
                                            <div class="card-body">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.</div>
                                        </div>`
                        },
                        {
                            'id': 'item_' + Math.random().toString(36).substr(2, 9),  // Genera un ID único
                            'title': `<div class="card card-custom">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h3 class="card-label">Basic Card
                                                    <small>sub title</small></h3>
                                                </div>
                                            </div>
                                            <div class="card-body">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.</div>
                                        </div>`
                        }
                    ]
                }, {
                    'id': '_columna61',
                    'title': '',
                    'class': '',
                    'item': [{
                            'id': 'item_' + Math.random().toString(36).substr(2, 9),  // Genera un ID único
                            'title': `<div class="card card-custom">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h3 class="card-label">Basic Card
                                                    <small>sub title</small></h3>
                                                </div>
                                            </div>
                                            <div class="card-body">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.</div>
                                        </div>`
                        },
                        {
                            'id': 'item_' + Math.random().toString(36).substr(2, 9),  // Genera un ID único
                            'title': `
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-success mr-3">
                                        <img alt="Pic" src="assets/media/users/300_20.jpg" />
                                    </div>
                                    <div class="d-flex flex-column align-items-start">
                                        <span class="text-dark-50 font-weight-bold mb-1">Product Team</span>
                                        <span class="label label-inline label-light-danger font-weight-bold">In progress</span>
                                    </div>
                                </div>
                            `,
                        }
                    ]
                }, {
                    'id': '_columna62',
                    'title': '',
                    'class': '',
                    'item': [{
                            'id': 'item_' + Math.random().toString(36).substr(2, 9),  // Genera un ID único
                            'title': `<div class="card card-custom">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h3 class="card-label">Basic Card
                                                    <small>sub title</small></h3>
                                                </div>
                                            </div>
                                            <div class="card-body">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.</div>
                                        </div>`
                        },
                        {
                            'id': 'item_' + Math.random().toString(36).substr(2, 9),  // Genera un ID único
                            'title': `
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-success mr-3">
                                        <img alt="Pic" src="assets/media/users/300_11.jpg" />
                                    </div>
                                    <div class="d-flex flex-column align-items-start">
                                        <span class="text-dark-50 font-weight-bold mb-1">SEO Optimization</span>
                                        <span class="label label-inline label-light-success font-weight-bold">In progress</span>
                                    </div>
                                </div>
                            `,
                        }
                    ]
                }
            ],
            dropBoard: function(el, source, sibling) {
                setTimeout(() => {
                    saveBoardOrder();
                }, 100);
            },
            dropEl: function(el, target, source, sibling) {
                console.log("Elemento movido:", el);
                setTimeout(() => {
                    saveItemOrder(); // Guardar el orden después de un pequeño retraso
                }, 100);
            }
        });

        // Guardar el orden de los boards (columnas)
        function saveBoardOrder() {
            var boardOrder = [];
            document.querySelectorAll('.kanban-board').forEach((board, index) => {
                boardOrder.push({
                    id: board.dataset.id, // Obtiene el ID de la columna desde el atributo data-id
                    order: index + 1
                });
            });

            let datax = [];
            datax.push({ name: "accion", value: "GUARDAR_ORDEN_BOARD" });
            datax.push({ name: "boardOrder", value: JSON.stringify(boardOrder) });

            // Guardar el orden en la base de datos
            $.ajax({
                url: 'controlador/contDashboard.php',
                method: 'POST',
                data: datax,
                success: function (response) {
                    console.log('Board order saved:', response);
                },
                error: function (xhr) {
                    console.error('Error saving board order:', xhr);
                }
            });
        }

        // Guardar el orden de los items
        function saveItemOrder() {
            var itemOrder = [];
            console.log("Guardando el orden de los elementos...");

            document.querySelectorAll('.kanban-board').forEach(board => {
                const boardId = board.dataset.id; // Obtiene el ID de la columna desde el atributo data-id
                board.querySelectorAll('.kanban-item').forEach((item, index) => {
                    console.log("Elemento en la columna", boardId, "con ID", item.dataset.eid, "en posición", index + 1);
                    itemOrder.push({
                        id: item.dataset.eid, // Obtiene el ID del elemento desde el atributo data-eid
                        boardId: boardId,
                        order: index + 1
                    });
                });
            });

            let datax = [];
            datax.push({ name: "accion", value: "GUARDAR_ORDEN_ITEM" });
            datax.push({ name: "itemOrder", value: JSON.stringify(itemOrder) });

            // Guardar el orden en la base de datos
            $.ajax({
                url: 'controlador/contDashboard.php',
                method: 'POST',
                data: datax,
                success: function (response) {
                    console.log('Item order saved:', response);
                },
                error: function (xhr) {
                    console.error('Error saving item order:', xhr);
                }
            });
        }




        $("[data-id=_columna12]").addClass('col-md-12');
        $("[data-id=_columna61]").addClass('col-md-6');
        $("[data-id=_columna62]").addClass('col-md-6');

        // Agregar la clase col-md-12 a los contenedores de columna (kanban-board)
        var kanbanColumns = document.querySelectorAll('.kanban-board');
        kanbanColumns.forEach(function (column) {
            // column.classList.add('col-md-12');
            column.classList.add('p-0');
            column.style.setProperty('margin-right', '0px', 'important');
        });

        var kanbanMain = document.querySelectorAll('.kanban-drag');
        kanbanMain.forEach(function (column) {
            column.classList.add('p-1');
        });

        var kanbanItem = document.querySelectorAll('.kanban-item');
        kanbanItem.forEach(function (column) {
            column.classList.add('p-0');
        });
    }


    // Public functions
    return {
        init: function() {
            _demo1();
        }
    };
}();

jQuery(document).ready(function() {
    KTKanbanBoardDemo.init();
});