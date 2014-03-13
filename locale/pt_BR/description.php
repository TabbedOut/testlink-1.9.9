<?php
/** 
 * ♔ TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 * 
 * Localization: Portuguese (pt_BR) texts - default development localization (World-wide English)
 *
 * 
 * The file contains global variables with html text. These variables are used as 
 * HELP or DESCRIPTION. To avoid override of other globals we are using "Test Link String" 
 * prefix '$TLS_hlp_' or '$TLS_txt_'. This must be a reserved prefix.
 * 
 * Contributors howto:
 * Add your localization to TestLink tracker as attachment to update the next release
 * for your language.
 *
 * No revision is stored for the the file - see CVS history
 * 
 * 
 * @package 	TestLink
 * @author 		Martin Havlat
 * @copyright 	2003-2009, TestLink community 
 * @version    	CVS: $Id: description.php,v 1.17 2010/09/13 09:52:42 mx-julian Exp $
 * @link 		http://www.teamst.org/index.php
 *
 * @internal Revisions:
 * 20130221 - mazin - Translation for Portuguese (pt_BR) version 1.9.5
 * 20111117 - pravato - Translation for Portuguese (pt_BR)
 * 20100409 - eloff - BUGID 3050 - Update execution help text
 **/
// printFilter.html
$TLS_hlp_generateDocOptions = "<h2>Opções para a geração do documento</h2>

<p>Esta tabela permite ao usuário filtrar os casos de teste antes de serem visualizados. 
Se selecionado (marcado) os dados serão exibidos. Para alterar os dados 
apresentados, marque ou desmarque clicando no Filtro, e selecione o nível 
desejado na árvore de dados.</p>

<p><b>Cabeçalho do Documento:</b> Os usuários podem filtrar informações no cabeçalho do documento. 
As informações do cabeçalho do documento incluem: Introdução, Escopo, 
Referências, Metodologia de Teste, e Limitações de Teste.</p>

<p><b>Corpo do Caso de Teste:</b> Os usuários podem filtrar informações do corpo do Caso de Teste. As informações do corpo do Caso de Teste 
incluem: Resumo, Passos, Resultados Esperados, e Palavras-chave</p>

<p><b>Resumo do Caso de Teste:</b> Os usuários podem filtrar informações do Resumo do Caso de Teste através do Título do Caso de Teste, 
no entanto, eles não podem filtrar informações do Resumo do Caso de Teste através do Corpo de um Caso de Teste. 
O resumo do Caso de Teste foi apenas parcialmente separado do corpo do Caso de Teste a fim de apoiar a visualização 
do Título com um breve resumo e a ausência de Passos, Resultados Esperados, 
e Palavras-chave. Se um usuário decidir ver o corpo do Caso de Teste, o Resumo do Caso de Teste 
será sempre incluído.</p>

<p><b>Tabela de Conteúdo:</b> O TestLink insere uma lista com todos os títulos com seus links internos checados.</p>

<p><b>Formatos de Saída:</b> Existem várias possibilidades: HTML, OpenOffice Writer, OpenOffice Calc, Excel, 
Word ou por E-mail (HTML).</p>";

// testPlan.html
$TLS_hlp_testPlan = "<h2>Plano de Teste</h2>

<h3>Geral</h3>
<p>O Plano de Teste é uma abordagem sistemática ao teste de um sistema de software. Você pode organizar a atividade de teste com 
 versões particulares do produto em tempo e resultados rastreáveis.</p>

<h3>Execução do Teste</h3>
<p>Esta é a seção onde os usuários podem executar os Casos de Teste (escrever os resultados dos testes) 
e imprimir a Suíte de Casos de Teste do Plano de Teste. Nesta seção os usuários podem 
acompanhar os resultados da sua execução dos Caso de Teste.</p> 

<h2>Gerenciamento do Plano de Teste</h2>
<p>Esta seção, somente acessível aos líderes, permite que os usuários possam administrar os planos de teste. 
A administração de planos de teste envolve a criação/edição/exclusão de planos, acréscimo/edição 
/exclusão/atualização dos casos de teste dos planos, criando versões, bem como definindo quem pode 
ver qual plano.<br />
Usuários com permissão de líder poderão também definir a prioridade/risco e a propriedade das 
suites de caso de teste (categorias) e criar marcos de teste.</p> 

<p>Nota: É possível que os usuários não possam ver uma lista suspensa que contenha os planos de teste. 
Nesta situação, todos os links (exceto para os líderes ativos) serão desvinculados. Se você 
estiver nesta situação, contate a administração do TestLink para lhe conceder os 
direitos de projeto adequado ou criar um Plano de Teste para você.</p>"; 

// custom_fields.html
$TLS_hlp_customFields = "<h2>Campos Personalizados</h2>
<p>Segue alguns fatos sobre a implementação de campos personalizados:</p>
<ul>
<li>Campos personalizados são definidos para todo o sistema.</li>
<li>Campos personalizados são associados ao tipo do elemento (Suíte de Teste, Caso de Teste).</li>
<li>Campos personalizados podem ser associados a múltiplos Projetos de Teste.</li>
<li>A sequência em que os campos personalizados serão exibidos pode ser diferente para cada Projeto de Teste.</li>
<li>Campos personalizados podem ser inativados para um Projeto de Teste específico.</li>
<li>O número de Campos personalizados não é restrito.</li>
</ul>

<p>A definição de um campo personalizado inclui os seguintes
atributos:</p>
<ul>
<li>Nome do Campo personalizado.</li>
<li>Capturar o nome da variável (ex: Este é o valor que é fornecido para a API lang_get(), 
ou exibido como se não for encontrado no arquivo de linguagem).</li>
<li>Tipo do Campo personalizado (string, numérico, float, enum, email).</li>
<li>Possibilidade de enumerar os valores (ex: RED|YELLOW|BLUE), aplicável a uma lista, lista de multiseleção 
e tipos de combo.<br />
<i>Utilize o caractere pipe ('|') para
separar os possíveis valores para uma enumeração. Um dos possíveis valores pode ser 
uma string vazia.</i>
</li>
<li>Valor default: NÃO IMPLEMENTADO AINDA.</li>
<li>Tamanho Mínimo/máximo para o valor do campo personalizado (utilize 0 para desativar). (NÃO IMPLEMENTADO AINDA).</li>
<li>Utilizar uma expressão regular para validar a entrada do usuário
(use <a href=\"http://au.php.net/manual/en/function.ereg.php\">ereg()</a>
syntax). <b>(NÃO IMPLEMENTADO AINDA)</b></li>
<li>Todos os campos personalizados são salvos como VARCHAR(255) na base de dados.</li>
<li>Exibição na Especificação do Teste.</li>
<li>Habilitado na Especificação do Teste. O usuário pode alterar o valor durante a Especificação do Caso de Teste.</li>
<li>Exibição na Execução do Teste.</li>
<li>Habilitado na Execução do Teste. O usuário pode alterar o valor durante a Execução do Caso de Teste.</li>
<li>Exibição no Planejamento do Plano de Teste.</li>
<li>Habilitado no Planejamento do Plano de Teste. O usuário pode alterar o valor durante o planejamento do Plano de Teste (adicionar Casos de Teste ao Plano de Teste).</li>
<li>Disponível para o usuário escolher o tipo de campo.</li>
</ul>
";

// execMain.html
$TLS_hlp_executeMain = "<h2>Executar Casos de Teste</h2>
<p>Permite aos usuários 'executar' os Casos de Teste. Execução propriamente 
dita é apenas a atribuição do resultado de um Caso de Teste (Passou, 
Com Falha ou Bloqueado) de uma compilação selecionada.</p>
<p>O acesso a um Bugtracking pode ser configurado. O usuário pode adicionar diretamente novos bugs e navegar pelos existentes. Consulte o manual de instalação para maiores detalhes.</p>";

//bug_add.html
$TLS_hlp_btsIntegration = "<h2>Adicionar Bugs ao Caso de Teste</h2>
<p><i>(Somente se estiver configurado)</i>
O TestLink possui uma integração muito simples com os sistemas de Bugtracking, 
mas não é capaz de enviar um pedido de abertura de bug ao Bugtracking ou receber de volta o ID do Bug. 
A integração é feita utilizando um link para a página do Bugtracking, com as seguintes características:
<ul>
	<li>Inserir novo Bug.</li>
	<li>Exibição das informações do bug. </li>
</ul>
</p>  

<h3>Processo para adicionar um novo bug</h3>
<p>
   <ul>
   <li>Passo 1: Utilize o link para abrir o Bugtracking e inserir um novo bug. </li>
   <li>Passo 2: Anote o ID do Bug gerado pelo Bugtracking.</li>
   <li>Passo 3: Escreva o ID do Bug no campo de entrada.</li>
   <li>Passo 4: Clique no botão Adicionar Bug</li>
   </ul>  

Depois de fechar a página de Adição de Bug, os dados relevantes do bug serão exibidos na página de execução.
</p>";

// execFilter.html
$TLS_hlp_executeFilter = "<h2>Configurações</h2>

<p>Em Configurações é possível que você selecione o plano de teste, a build e 
a plataforma (se disponível) para ser executado.</p>

<h3>Plano de Teste</h3>
<p>Você pode escolher o Plano de Teste necessário. De acordo com o plano de teste escolhido, as apropriadas 
builds serão exibidas. Depois de escolher um plano de teste, os filtros serão reiniciados.</p>

<h3>Plataformas</h3>
<p>Se o recurso de plataformas é usado, você deve selecionar a plataforma apropriada antes da execução.</p>

<h3>Execução do Build</h3>
<p>Você pode escolher o Build em que deseja executar os Casos de Teste.</p>

<h2>Filtros</h2>
<p>Filtros proporcionam a oportunidade de influenciar ainda mais o conjunto de casos de teste mostrados.
Através dos Filtros é possível diminuir o conjunto de Casos de Teste exibidos. Selecione 
os filtros desejados e clique no botão \"Aplicar\".</p>

<p>Os Filtros Avançados permitem que você especifique um conjunto de valores para filtros aplicáveis 
​​usando Ctrl + Clique dentro de cada ListBox.</p>


<h3>Filtro de Palavra-chave</h3>
<p>Você pode filtrar os Casos de Teste pelas Palavras-chave que foram atribuídas. Você pode escolher " .
"múltiplas Palavras-chave utilizando Ctrl + Clique. Se você escolher mais de uma palavra-chave, você pode " .
"decidir se somente serão exibidos os Casos de Teste que contém todas as Palavras-chave aselecionadas " .
"(botão \"E\") ou pelo menos uma das Palavras-chave escolhidas (botão \"OU\").</p>

<h3>Filtro de Prioridade</h3>
<p>Você pode filtrar os Casos de Teste pela prioridade do Teste. A proridade do Teste é a \"importância do Caso de Teste\" " .
"combinado com \"a urgência do Teste\" dentro do Plano de Teste atual.</p> 

<h3>Filtro de Usuário</h3>
<p>Você pode filtrar os Casos de Teste que não estão atribuídos (\"Ninguém\") ou atribuídos a \"Alguém\". " .
"Você também pode filtrar os Casos de Teste que são atribuídos a um testador específico. Se você escolheu um testador " .
"específico, também existe a possibilidade de mostrar os Casos de Teste que estão por serem atribuídos " .
"(Filtros avançados estão disponíveis).</p>

<h3>Filtro de Resultado</h3>
<p>Você pode filtrar os Casos de Teste pelos resultados (Filtros avançados estão disponíveis). Você pode filtrar por " .
"resultado \"na build escolhida para a execução\", \"na última execução\", \"em TODAS as Builds\", " .
"\"em QUALQUER build\" e \"em uma build específica\". Se \"uma build específica\" for escolhida, então você pode " .
"especificar a build. </p>";


// newest_tcversions.html
$TLS_hlp_planTcModified = "<h2>Versões mais recentes do Caso de Teste</h2>
<p>Todo o conjunto de Casos de Teste ligados ao Plano de Teste é analisado, e uma lista de Casos 
de Teste que têm uma versão mais recente é exibida (contra o conjunto atual do Plano de Teste).
</p>";


// requirementsCoverage.html
$TLS_hlp_requirementsCoverage = "<h3>Cobertura de Requisitos</h3>
<br />
<p>Este recurso permite mapear uma cobertura de usuário ou requisitos do sistema 
por Casos de Teste. Navegue através do link \"Especificar Requisitos\" na tela principal.</p>

<h3>Especificação de Requisitos</h3>
<p>Os Requisitos estão agrupados no documento 'Especificação de Requisitos' que está relacionado ao 
Projeto de Teste.<br /> O TestLink ainda não suporta versões para a Especificação de Requisitos e   
também para os Requisitos. Assim, a versão do documento deve ser adicionada depois do 
<b>Título</b> da Especificação.
O usuário pode adicionar uma descrição simples ou uma nota no campo <b>Escopo</b>.</p> 

<p>Sobrescrever o contador de Requisitos serve para avaliar a cobertura dos requisitos no caso 
de nem todos os requisitos estarem adicionados ao TestLink.
<p>O valor <b>0</b> significa que a contagem atual de requisitos é usado para métricas.</p> 
<p><i>Ex: SRS inclui 200 requisitos, mas somente 50 são adicionados ao Plano de Teste. A cobertura de testes 
é de 25% (se todos estes requisitos forem testados).</i></p>

<h3>Requisitos</h3>
<p>Clique no título para criar uma Especificação de Requisitos. Você pode criar, editar, deletar 
ou importar requisitos para este documento. Cada Requisito tem título, escopo e status.
O status deve ser \"Válido\" ou \"Não testado\". Requisitos não testados não são contabilizados
para as métricas. Este parâmetro deve ser utilizado para características não implementadas 
e requisitos modelados incorretamente.</p> 

<p>Você pode criar novos Casos de Teste para os requisitos utilizando multi ações para os requisitos 
ativos na tela de especificação de requisitos. Estes Casos de Teste são criados dentro da Suíte de 
Teste com nome definido na configuração <i>(padrão é: &#36;tlCfg->req_cfg->default_testsuite_name = 
\"Test suite created by Requirement - Auto\";)</i>. Título e Escopo são copiados destes Casos de Teste.</p>
";

$TLS_hlp_req_coverage_table = "<h3>Cobertura:</h3>
Um valor por ex. de \"40% (8/20)\" significa que 20 Casos de Teste devem ser criados para testar completamente este
Requisito. 8 destes já foram criados e associados ao Requisito, com 
a cobertura de 40 %.
";


// req_edit
$TLS_hlp_req_edit = "<h3>Links internos no Escopo:</h3>
<p>Links internos servem ao propósito da criação de links a outros requisitos / especificações de requisitos 
com uma sintaxe especial. O comportamento dos Links internos pode ser alterado no arquivo de configuração.
<br /><br />
<b>Uso:</b>
<br />
Link para Requisitos: [req]req_doc_id[/req]<br />
Link para Especificação de Requisitos: [req_spec]req_spec_doc_id[/req_spec]</p>

<p>O Projeto de Teste do Requisito / Especificação de Requisitos, uma versão e uma âncora 
também podem ser especificados:<br />
[req tproj=&lt;tproj_prefix&gt; anchor=&lt;anchor_name&gt; version=&lt;version_number&gt;]req_doc_id[/req]<br />
Esta sintaxe também funciona para as especificações de requisito (atributos de versão não tem nenhum efeito).<br />
Se você não especificar a versão do Requisito completo, todas as versões serão exibidas.</p>

<h3>Log para mudanças:</h3>
<p>Sempre que uma alteração é feita, o Testlink irá pedir uma mensagem de log. Esta mensagem de log serve como rastreabilidade.
Se apenas o escopo do Requisito mudou, você pode decidir se deseja criar uma nova revisão ou não.
Sempre que alguma coisa além do escopo é alterado, você é forçado a criar uma nova revisão.</p>
";


// req_view
$TLS_hlp_req_view = "<h3>Links Diretos:</h3>
<p>É fácil compartilhar este documento com outros, basta clicar no ícone do globo no topo deste documento para criar um link direto.</p>

<h3>Ver Histórico:</h3>
<p>Este recurso permite comparar revisões/versões de requisitos, se mais de uma revisão/versão de requisitos existir.
A visão geral fornece uma mensagem de log para cada revisão/versão, um timestamp e autor da última alteração.</p>

<h3>Cobertura:</h3>
<p>Exibir todos os Casos de Teste associados para este Requisito.</p>

<h3>Relações:</h3>
<p>Relações de Requisitos são usados ​​para relacionamentos de modelos entre os requisitos.
Relações personalizadas e a opção de permitir relações entre os requisitos de
diferentes projetos de teste podem ser configurados no arquivo de configuração.
Se você definir a relação \"Requisito A é pai do Requisito B\", 
o Testlink irá definir a relação \"Requisito B é filho do Requisito A\" implicitamente.</p>
";


// req_spec_edit
$TLS_hlp_req_spec_edit = "<h3>Links internos no Escopo:</h3>
<p>Links internos servem ao propósito da criação de links a outros requisitos / especificações de requisitos 
com uma sintaxe especial. O comportamento dos Links internos pode ser alterado no arquivo de configuração.
<br /><br />
<b>Uso:</b>
<br />
Link para Requisitos: [req]req_doc_id[/req]<br />
Link para Especificação de Requisitos: [req_spec]req_spec_doc_id[/req_spec]</p>

<p>O Projeto de Teste do Requisito / Especificação de Requisitos, uma versão e uma âncora 
também podem ser especificados:<br />
[req tproj=&lt;tproj_prefix&gt; anchor=&lt;anchor_name&gt; version=&lt;version_number&gt;]req_doc_id[/req]<br />
Esta sintaxe também funciona para as especificações de requisito (atributos de versão não tem nenhum efeito).<br />
Se você não especificar a versão do Requisito completo, todas as versões serão exibidas.</p>
";


// planAddTC_m1.tpl
$TLS_hlp_planAddTC = "<h2>Sobre 'Campos personalizados salvos'</h2>
Se você tiver definido e atribuído ao Projeto de Teste,<br /> 
Campos Personalizados com:<br />
 'Exibição no desenho do Plano de Teste=true' e <br />
 'Habilitar no desenho do Plano de Teste=true'<br />
você irá ver nesta página APENAS os Casos de Teste ligados ao Plano de Teste.
";


// resultsByTesterPerBuild.tpl
$TLS_hlp_results_by_tester_per_build_table = "<b>Mais informações sobre os testadores</b><br />
Se você clicar no nome do testador nesta tabela, você irá ter uma visão mais detalhada
sobre todos os Casos de Teste atribuídos para esse usuário e o seu progresso de teste.<br /><br />
<b>Nota:</b><br />
Este relatório mostra os casos de teste, que são atribuídos a um usuário específico e foram executados
com base em cada build ativo. Mesmo se um caso de teste foi executado por outro usuário que não o usuário atribuído,
o caso de teste irá aparecer como executado pelo usuário atribuído.
";


// xxx.html
//$TLS_hlp_xxx = "";

// ----- END ------------------------------------------------------------------
?>
