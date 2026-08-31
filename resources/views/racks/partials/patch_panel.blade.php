<script src="https://d3js.org/d3.v7.min.js"></script>
<style>
    .port {
    stroke: black;
    }
    .label {
    font-size: 14px;
    font-weight: bold;
    }
    .legend {
    font-size: 12px;
    }
    .tooltip {
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

<div class="tooltip" id="tooltip"></div>
<svg id="rack" width="100%" height="{{ $rack->patchPanels->sum(fn($p) => ceil($p->qtde_portas/24)*70) + 50 }}"></svg>

<script>
    const svg = d3.select("#rack");
    const tooltip = d3.select("#tooltip");

    function drawPatchPanel(svg, x, y, panelLabel, id, totalPorts, portas_habilitadas) {
      const panelWidth = 480;
      const panelHeight = Math.ceil(totalPorts/24)*40 ; // quantidade de U's no rack
      const portsPerRow = 24;

      // Fundo do patch panel
      svg.append("rect")
        .attr("x", x)
        .attr("y", y)
        .attr("width", panelWidth)
        .attr("height", panelHeight)
        .attr("fill", "black")
        .attr("rx", 5)
        .attr("ry", 5);

      // Label
      svg.append("text")
        .attr("x", x + panelWidth / 2)
        .attr("y", y - 10)
        .attr("text-anchor", "middle")
        .attr("class", "label")
        .text("Patch Panel " + panelLabel);

      const ports = d3.range(totalPorts);
      const tooltipTexts = portas_habilitadas;
      /*const tooltipTexts = {
        5: "Essa porta está boa",
        8: "Essa porta está queimada"
      };*/
      
      const portas_habilitadas_string = Object.keys(portas_habilitadas);
      const portas_habilitadas_int = portas_habilitadas_string.map(num => Number(num));

      svg.selectAll(`.port-${id}`)
        .data(ports)
        .enter()
        .append("rect")
        .attr("x", (d, i) => x + 10 + (i % portsPerRow) * 19)
        .attr("y", (d, i) => y + 10 + Math.floor(i / portsPerRow) * 40)
        .attr("width", 18)
        .attr("height", 18)
        .attr("fill", d => (portas_habilitadas_int.includes(d) ? "green" : "gray"))
        .attr("class", "port")
        .on("mouseover", function(event, d) {
          if (tooltipTexts[d]) {
            tooltip.style("opacity", 1).text(tooltipTexts[d]);
          }
        }).on("mousemove", function(event) {
          tooltip.style("left", (event.pageX + 10) + "px").style("top", (event.pageY + -200) + "px");
        }).on("mouseout", function() {
            tooltip.style("opacity", 0);
        }
      );
    
      // Numerar portas
      svg.selectAll(`.port-label-${id}`)
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

    // Legenda
    const legendData = [
      { color: "green", text: "Porta em uso" },
      { color: "gray", text: "Porta livre" }
    ];

    const legend = svg.selectAll(".legend")
      .data(legendData)
      .enter()
      .append("g")
      .attr("class", "legend")
      .attr("transform", (d, i) => `translate(400,${30 + i * 25})`);

    legend.append("rect")
      .attr("width", 14)
      .attr("height", 14)
      .attr("fill", d => d.color)
      .attr("stroke", "black");

    legend.append("text")
      .attr("x", 20)
      .attr("y", 11)
      .text(d => d.text);

    @php $y = 50; @endphp
    @foreach($rack->patchPanels as $patchPanel)
        // pegando portas habilitadas
        @php
        $portas_habilitadas = [];
        foreach($patchPanel->salasVinculadas as $sala){
            $portas_habilitadas[$sala->pivot->porta-1] = $sala->nome;
          }
        @endphp

        drawPatchPanel(svg, 20, {{$y}}, '{{ $patchPanel->nome }}', {{$patchPanel->id}}, {{ $patchPanel->qtde_portas }}, @json($portas_habilitadas) );
        
        @php $y += ceil($patchPanel->qtde_portas/24)*70; @endphp
    @endforeach

</script>