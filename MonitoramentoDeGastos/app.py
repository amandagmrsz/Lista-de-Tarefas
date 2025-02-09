from flask import Flask, request, render_template
import dash
from dash import dcc, html, Input, Output, dash_table
import dash_bootstrap_components as Dcb
from bcb import sgs
import plotly.express as Px
import pandas as pd
import yfinance as yf
from openpyxl import load_workbook
from flask import Flask, request, send_file, redirect, url_for
import xlwings as xw
import bd  # Importando o arquivo bd.py



app = Flask(__name__)

@app.route("/")
def home():
    return render_template("index.html")

#Investimento
app_dash = dash.Dash(
    __name__,
    server=app,
    routes_pathname_prefix="/dash/",
    external_stylesheets=[Dcb.themes.DARKLY]
)

def DadosInvestimento(CapitalInicial, DataInicio, DataFim, Frequencia, Risco, InvestimentoMensal):
    # Obtemos as taxas da SELIC e CDI usando a API do BCB
    Taxas = sgs.get({'SELIC': 11, 'CDI': 12}, start=DataInicio, end=DataFim)
    
    if Risco == 'Baixo':
        Taxa = Taxas['SELIC']
    elif Risco == 'Médio':
        Percentual_CDI = 1.20
        Taxa = Taxas['CDI'] * Percentual_CDI
    elif Risco == 'Alto':
        BitcoinData = yf.download('BTC-USD', start=DataInicio, end=DataFim)
        BitcoinData['Variação'] = BitcoinData['Adj Close'].pct_change().fillna(0) * 100
        Taxa = BitcoinData['Variação']
    elif Risco == 'CDI (110%)':
        Percentual_CDI = 1.10
        Taxa = Taxas['CDI'] * Percentual_CDI
    else:
        raise ValueError("Tipo de investimento inválido.")
    
    Multiplicador = 1 + (Taxa / 100)
    CapitalAcumulado = pd.Series([CapitalInicial], index=[DataInicio])

    for i in range(1, len(Taxa)):
        if CapitalAcumulado.index[-1] >= DataFim:
            break
        NovoCapital = CapitalAcumulado.iloc[i-1] * (1 + Taxa.iloc[i-1] / 100) + InvestimentoMensal
        CapitalAcumulado = pd.concat([CapitalAcumulado, pd.Series([NovoCapital], index=[CapitalAcumulado.index[-1] + pd.DateOffset(months=1)])])

    if Risco == 'CDI (110%)' and CapitalAcumulado.index[-1] < DataFim:
        UltimaTaxa = Taxa.iloc[-1]
        while CapitalAcumulado.index[-1] < DataFim:
            NovoCapital = CapitalAcumulado.iloc[-1] * (1 + UltimaTaxa / 100) + InvestimentoMensal
            CapitalAcumulado = pd.concat([CapitalAcumulado, pd.Series([NovoCapital], index=[CapitalAcumulado.index[-1] + pd.DateOffset(months=1)])])

    CapitalComFrequenciaEscolhida = CapitalAcumulado.resample(Frequencia).last().dropna()
        
    TotalInvestido = CapitalInicial + InvestimentoMensal * (len(CapitalComFrequenciaEscolhida) - 1)
    Lucro = CapitalComFrequenciaEscolhida.iloc[-1] - TotalInvestido

    return CapitalComFrequenciaEscolhida, TotalInvestido, Lucro

navbar = html.Nav(
    children=[
        html.Ul(
            children=[
                html.Li(html.A("Home", href="/", style={'color': 'white', 'textDecoration': 'none', 'padding': '20px'})),
                html.Li(html.A("Investimentos", href="/dash/", style={'color': 'white', 'textDecoration': 'none', 'padding': '20px'})),
                html.Li(html.A("Holerite", href="/holerite", style={'color': 'white', 'textDecoration': 'none', 'padding': '20px'})),
                html.Li(html.A("Monitoramento de Gastos", href="/gastos", style={'color': 'white', 'textDecoration': 'none', 'padding': '2  0px'})),
            ],
            style={'display': 'flex', 'listStyle': 'none', 'justifyContent': 'center', 'background': '#333', 'margin': 0}
        )
    ]
)

app_dash.layout = html.Div([ 
    navbar,  # Adiciona o navbar no layout
    Dcb.Container([  # Aqui está o Container, onde você pode usar fluid=True
        Dcb.Row([  # Criei uma linha que contém duas colunas
            Dcb.Col([ 
                html.H1("Parâmetros de Entrada", style={'font-weight': 'bold', 'color': 'lightblue'}),
                Dcb.Row([ 
                    Dcb.Col([ 
                        Dcb.Label("Capital Inicial"), 
                        Dcb.Input(id="CapitalInicial", placeholder="Capital Inicial", type="number", value=1000), 
                    ]) 
                ], className="mb-2"), 
                Dcb.Row([ 
                    Dcb.Col([ 
                        Dcb.Label("Investimento Mensal"), 
                        Dcb.Input(id="InvestimentoMensal", placeholder="Investimento Mensal", type="number", value=0), 
                    ]) 
                ], className="mb-2"), 
                Dcb.Row([ 
                    Dcb.Col([ 
                        Dcb.Label("Data Início"), 
                    ], width='auto'), 
                    Dcb.Col([ 
                        dcc.DatePickerSingle(id="DataInicio", date="2020-01-01", display_format='YYYY-MM-DD'), 
                    ]) 
                ], className="mb-3"), 
                Dcb.Row([ 
                    Dcb.Col([ 
                        Dcb.Label("Data de Fim"), 
                    ], width='auto'), 
                    Dcb.Col([ 
                        dcc.DatePickerSingle(id="DataFim", date="2024-01-01", display_format='YYYY-MM-DD'), 
                    ]) 
                ], className="mb-3"), 
                Dcb.Row([ 
                    Dcb.Col([ 
                        Dcb.Label("Frequência"), 
                        Dcb.Select( 
                            id="Frequencia", 
                            options=[ 
                                {'label': 'Dia', 'value': 'D'}, 
                                {'label': 'Mês', 'value': 'M'}, 
                                {'label': 'Ano', 'value': 'Y'} 
                            ], 
                            value='M', 
                        ), 
                    ]) 
                ], className="mb-2"), 
                Dcb.Row([ 
                    Dcb.Col([ 
                        Dcb.Label("Tipo de Investimento"), 
                        Dcb.Select( 
                            id="TipoRisco", 
                            options=[ 
                                {'label': 'Baixo (SELIC)', 'value': 'Baixo'}, 
                                {'label': 'Médio (CDB)', 'value': 'Médio'}, 
                                {'label': 'Alto (Bitcoin)', 'value': 'Alto'}, 
                                {'label': 'CDI (110%)', 'value': 'CDI (110%)'} 
                            ], 
                            value='Baixo', 
                        ), 
                    ]) 
                ], className="mb-2"), 
                Dcb.Button("Calcular", id="CalcularButton", color="primary", className="mt-2"), 
                html.Div(id="ResumoInvestimento", className="mt-3")  # Div para mostrar o total investido e lucro
            ], width=4, className="border p-3 bf-dark text-white"), 
            
            Dcb.Col([  # Tabela e gráfico ficam lado a lado nesta coluna
                dcc.Graph(id="GraficoLucro"), 
                html.Div(id="TabelaLucro", className="mt-3") 
            ], width=8, className="border p-3 bf-dark text-white") 
        ], className="mt-3")
    ], fluid=True)  # Aqui você pode usar fluid=True dentro do Container
])

def AjustarPorInflacao(Capital, DataFim):
    # Obter a inflação acumulada até o mês de DataFim
    IPCA = sgs.get({'IPCA': 433}, start=DataFim, end=DataFim)
    if IPCA.empty:
        raise ValueError("Não foi possível obter a inflação para a data fornecida.")
    
    Inflacao = IPCA['IPCA'].iloc[0]
    
    # Ajuste do capital pela inflação (deduzindo a inflação)
    CapitalAjustado = Capital - (Capital * Inflacao / 100)
    return CapitalAjustado, Inflacao

@app_dash.callback(      
    [Output("GraficoLucro", "figure"),
     Output("TabelaLucro", "children"),
     Output("ResumoInvestimento", "children")],  
    [Input("CapitalInicial", "value"),
     Input("InvestimentoMensal", "value"),
     Input("DataInicio", "date"),
     Input("DataFim", "date"),
     Input("Frequencia", "value"),
     Input("TipoRisco", "value"),
     Input("CalcularButton", "n_clicks")]
)
def AtualizarGraficoETabela(CapitalInicial, InvestimentoMensal, DataInicio, DataFim, Frequencia, TipoRisco, NClicks):
    # Se o botão não foi clicado, retorne dash.no_update para todos os outputs
    if not NClicks:
        return dash.no_update, dash.no_update, dash.no_update
    
    DataInicio = pd.to_datetime(DataInicio)
    DataFim = pd.to_datetime(DataFim)
    
    SerieLucros, TotalInvestido, Lucro = DadosInvestimento(CapitalInicial, DataInicio, DataFim, Frequencia, TipoRisco, InvestimentoMensal)
    SerieLucros = SerieLucros.reset_index()
    SerieLucros.columns = ["Data", "LucroAcumulado"]
    SerieLucros["LucroAcumulado"] = SerieLucros["LucroAcumulado"].round(2)
    SerieLucros["Data"] = SerieLucros["Data"].dt.date
    
    # Calcular o Lucro Real ajustado pela inflação
    LucroReal, Inflacao = AjustarPorInflacao(SerieLucros['LucroAcumulado'].iloc[-1], DataFim)
    
    Fig = Px.line(SerieLucros, x="Data", y="LucroAcumulado", title=f"Lucro Acumulado ({TipoRisco} Risco)")
    
    Tabela = dash_table.DataTable(
        data=SerieLucros.to_dict('records'),
        columns=[{"name": i, "id": i} for i in ['Data', 'LucroAcumulado']],
        style_header={'backgroundColor': 'rgb(30, 30, 30)', 'color': 'white', 'textAlign': 'center'},
        style_data={'color': 'black', 'backgroundColor': 'white', 'textAlign': 'center'},
        style_table={'maxHeight': '400px', 'overflowY': 'scroll'}
    )
    
    # Resumo com o Lucro Real ajustado pela inflação
    Resumo = html.Div([
        html.H1("Resumo do Investimento", style={'font-weight': 'bold', 'color': 'lightblue'}),
        html.H5(f"Total Investido: R$ {TotalInvestido:.2f}", style={'font-weight': 'bold', 'color': 'lightgreen'}),
        html.H5(f"Lucro: R$ {Lucro:.2f}", style={'font-weight': 'bold', 'color': 'orange'}),
        html.H5(f"Total (Investido + Lucro): R$ {TotalInvestido + Lucro:.2f}", style={'font-weight': 'bold', 'color': 'purple'}),
        html.H5(f"Lucro Real (ajustado pela inflação): R$ {LucroReal:.2f}", style={'font-weight': 'bold', 'color': 'red'}),
        html.H5(f"Inflação do período: {Inflacao:.2f}%", style={'font-weight': 'bold', 'color': 'white'}),
    ], style={'margin-top': '20px'})

    return Fig, Tabela, Resumo

#Holerite

# Função para processar os dados e salvar no Excel
def processar_arquivo_excel(nome_empresa, cnpj_empresa, data_holerite, matricula, nome_funcionario, salario, cargo, admissao, beneficios, descontos):
    # Abrir o arquivo Excel
    wb = load_workbook(r"C:\Users\MXM\Downloads\APP PYTHON\APP PYTHON\holerite.xlsx")
    aba_holerite = wb["contracheque"]  
    
    # Atualizar informações da empresa
    aba_holerite['C3'] = nome_empresa
    aba_holerite['D5'] = cnpj_empresa
    aba_holerite['G5'] = data_holerite
    
    # Atualizar informações do funcionário
    aba_holerite['C8'] = matricula
    aba_holerite['D8'] = nome_funcionario
    aba_holerite['H14'] = salario
    aba_holerite['H7'] = cargo
    aba_holerite['C10'] = admissao
    
    # Atualizar os benefícios
    linha_beneficio = 15  # Linha inicial para benefícios
    for beneficio in beneficios:
        aba_holerite[f'D{linha_beneficio}'] = beneficio['nome_beneficio']
        aba_holerite[f'H{linha_beneficio}'] = beneficio['vencimento']
        linha_beneficio += 1
    
    # Atualizar os descontos
    linha_desconto = linha_beneficio + 2  # Deixe espaço entre benefícios e descontos
    for desconto in descontos:
        aba_holerite[f'D{linha_desconto}'] = desconto['nome_desconto']        
        aba_holerite[f'K{linha_desconto}'] = desconto['desconto'] 
        linha_desconto += 1
    
    # Salvar o arquivo atualizado
    wb.save(r"C:\Users\Amanda Guimarães\Desktop\APP PYTHON\holerite.xlsx")

# Rota para exibir o formulário
@app.route('/holerite', methods=['GET'])
def holerite():
    return render_template('holerite.html')

# Rota para processar o formulário
@app.route('/processar_holerite', methods=['POST'])
def processar_holerite():
    # Receber dados do formulário
    nome_empresa = request.form['nome_empresa']
    cnpj_empresa = request.form['cnpj_empresa']
    data_holerite = request.form['data_holerite']
    matricula = request.form['matricula']
    nome_funcionario = request.form['nome_funcionario']
    salario = float(request.form['salario']) if request.form['salario'].strip() else 0.0
    cargo = request.form['cargo']
    admissao = request.form['admissao']
    
    # Processar os benefícios
    beneficios = []
    for i in range(1, 11):  # Até 10 benefícios
        nome_beneficio = request.form.get(f'nome_beneficio_{i}')
        vencimento = request.form.get(f'vencimento_{i}')
        if nome_beneficio:
            beneficios.append({'nome_beneficio': nome_beneficio, 'vencimento': vencimento})
    
    # Processar os descontos
    descontos = []
    for i in range(1, 11):  # Até 10 descontos
        nome_desconto = request.form.get(f'nome_desconto{i}')
        desconto = request.form.get(f'desconto_{i}')
        if nome_desconto:
            descontos.append({'nome_desconto': nome_desconto, 'desconto': desconto})
    
    # Processar o arquivo Excel
    processar_arquivo_excel(nome_empresa, cnpj_empresa, data_holerite, matricula, nome_funcionario, salario, cargo, admissao, beneficios, descontos)
    
    return redirect(url_for('holerite'))


#Monitoramento Financeiro

@app.route('/gastos', methods=['GET'])
def mostrar_gastos():
    # Renderiza o arquivo gastos.html
    return render_template('gastos.html')

@app.route('/salvar_receitas',  methods=['GET', 'POST'])
def salvar_receitas():
    # Capturando todos os campos do formulário
    dados_receitas = {}
    
    for key in request.form:
        dados_receitas[key] = request.form[key]

    # Chama a função salvar_no_bd para salvar os dados no banco
    bd.salvar_no_bd(dados_receitas)

    # Após salvar as receitas, redireciona para a página de monitoramento
    return redirect(url_for('monitoramento'))

@app.route('/monitoramento', methods=['GET'])
def mostrar_monitoramento():
    ## Pega os dados diretamente do banco/arquivo (chama a função do bd.py)
    dados = bd.obter_dados()
    
    # Calculando os totais
    total_receitas = sum(dados['RECEITAS'].values())
    total_despesas_fixas = sum(dados['DESPESAS FIXAS'].values())
    saldo = total_receitas - total_despesas_fixas

    # Passando os dados para o template
    return render_template('monitoramento.html', dados=dados, total_receitas=total_receitas, total_despesas_fixas=total_despesas_fixas, saldo=saldo)

if __name__ == "__main__":
    app.run(debug=True)
