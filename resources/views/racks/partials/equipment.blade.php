<script src="https://d3js.org/d3.v7.min.js"></script>
<style>
    .equipment-port {
        stroke: black;
    }
    .equipment-label {
        font-size: 14px;
        font-weight: bold;
    }
    .equipment-legend {
        font-size: 12px;
    }
    .equipment-tooltip {
        position: absolute;
        background: rgba(0,0,0,0.8);
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s;
    }
</style>

<div class="equipment-tooltip" id="equipment-tooltip"></div>
<svg id="equipment-rack" width="100%" height="{{ $rack->equipamentos->sum(fn($e) => ceil(($e->modeloSwitch?->qtde_portas ?? 24)/24) * 70 + 30) + 50 }}"></svg>

<script>
    const equipmentSvg = d3.select("#equipment-rack");
    const equipmentTooltip = d3.select("#equipment-tooltip");

    function drawEquipment(svg, x, y, hostname, model, id, totalPorts) {
        const equipmentWidth = 480;
        const equipmentHeight = Math.ceil(totalPorts/24) * 40;
        const portsPerRow = 24;

        // Fundo do equipamento
        svg.append("rect")
            .attr("x", x)
            .attr("y", y)
            .attr("width", equipmentWidth)
            .attr("height", equipmentHeight)
            .attr("fill", "#2c3e50")
            .attr("rx", 5)
            .attr("ry", 5);

        // Label do equipamento
        svg.append("text")
            .attr("x", x + equipmentWidth / 2)
            .attr("y", y - 10)
            .attr("text-anchor", "middle")
            .attr("class", "equipment-label")
            .text(hostname);

        // Modelo do equipamento
        svg.append("text")
            .attr("x", x + equipmentWidth / 2)
            .attr("y", y - 25)
            .attr("text-anchor", "middle")
            .attr("font-size", "10px")
            .attr("fill", "#7f8c8d")
            .text(model);

        const ports = d3.range(totalPorts);
        
        // Todas as portas começam como livres (cinza)
        // Quando o sistema de vínculo estiver pronto, adicionar lógica similar ao patch panel
        const portas_ocupadas = []; // Array vazio por enquanto
        const portas_ocupadas_int = portas_ocupadas.map(num => Number(num));

        svg.selectAll(`.equipment-port-${id}`)
            .data(ports)
            .enter()
            .append("rect")
            .attr("x", (d, i) => x + 10 + (i % portsPerRow) * 19)
            .attr("y", (d, i) => y + 10 + Math.floor(i / portsPerRow) * 40)
            .attr("width", 18)
            .attr("height", 18)
            .attr("fill", d => (portas_ocupadas_int.includes(d) ? "green" : "gray"))
            .attr("class", "equipment-port")
            .on("mouseover", function(event, d) {
                const status = portas_ocupadas_int.includes(d) ? "Porta em uso" : "Porta livre";
                const tooltipText = `Porta ${d + 1} - ${status}`;
                equipmentTooltip.style("opacity", 1).text(tooltipText);
            })
            .on("mousemove", function(event) {
                equipmentTooltip.style("left", (event.pageX + 10) + "px").style("top", (event.pageY - 200) + "px");
            })
            .on("mouseout", function() {
                equipmentTooltip.style("opacity", 0);
            });

        // Numerar portas
        svg.selectAll(`.equipment-port-label-${id}`)
            .data(ports)
            .enter()
            .append("text")
            .attr("x", (d, i) => x + 19 + (i % portsPerRow) * 19)
            .attr("y", (d, i) => y + 23 + Math.floor(i / portsPerRow) * 40)
            .attr("text-anchor", "middle")
            .attr("alignment-baseline", "middle")
            .attr("font-size", "8px")
            .attr("fill", "white")
            .text(d => d + 1);
    }

    // Legenda para equipamentos
    const equipmentLegendData = [
        { color: "green", text: "Porta em uso" },
        { color: "gray", text: "Porta livre" }
    ];

    const equipmentLegend = equipmentSvg.selectAll(".equipment-legend")
        .data(equipmentLegendData)
        .enter()
        .append("g")
        .attr("class", "equipment-legend")
        .attr("transform", (d, i) => `translate(400, ${30 + i * 25})`);

    equipmentLegend.append("rect")
        .attr("width", 14)
        .attr("height", 14)
        .attr("fill", d => d.color)
        .attr("stroke", "black");

    equipmentLegend.append("text")
        .attr("x", 20)
        .attr("y", 11)
        .text(d => d.text);

    // Desenhar equipamentos
    @php $equipmentY = 50; @endphp
    @foreach($rack->equipamentos as $equipamento)
        @php $portCount = $equipamento->modeloSwitch?->qtde_portas ?? 24; @endphp
        drawEquipment(
            equipmentSvg, 
            20, 
            {{ $equipmentY }}, 
            '{{ $equipamento->hostname }}', 
            '{{ $equipamento->modeloSwitch?->nome ?? 'Sem modelo' }}', 
            {{ $equipamento->id }}, 
            {{ $portCount }}
        );
        
        @php 
            $equipmentY += ceil($portCount/24) * 70 + 30; // Espaço extra entre equipamentos
        @endphp
    @endforeach
</script>