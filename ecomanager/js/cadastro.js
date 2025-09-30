document.getElementById("cadastrar").addEventListener("click", async () => {
    const nome = document.getElementById("nome").value;
    const email = document.getElementById("email").value;
    const senha = document.getElementById("senha").value;

    const fd = new FormData();
    fd.append("nome", nome);
    fd.append("email", email);
    fd.append("senha", senha);

    const retorno = await fetch("../php/cadastro.php", { method: "POST", body: fd });
    const resposta = await retorno.json();

    if(resposta.ok){
        alert("Cadastro realizado com sucesso!");
        window.location.href = "login.html";
    } else {
        alert("Erro: " + resposta.msg);
    }
});
