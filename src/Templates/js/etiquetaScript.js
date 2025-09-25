const loadKonva = () => {
    const json = '{"attrs":{"width":400,"height":400},"className":"Stage","children":[{"attrs":{},"className":"Layer","children":[{"attrs":{"x":100,"y":100,"radius":50,"fill":"red","stroke":"black","strokeWidth":3},"className":"Circle"}]}]}';

    Konva.Node.create(json, 'container');
}

document.addEventListener('DOMContentLoaded', () => {
    loadKonva();

    const container = document.getElementById('container');
    const p = document.createElement("p");
    p.textContent = 'Canvas Konva carregado com sucesso!';
    p.style.margin = '20px';

    container.appendChild(p);
});s