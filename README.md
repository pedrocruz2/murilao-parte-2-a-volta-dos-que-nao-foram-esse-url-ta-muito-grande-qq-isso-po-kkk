# Atividade Ponderada: Cluster Kubernetes com HPA

Este repositório contém os arquivos para criar um cluster Kubernetes localmente usando `kind`, implantar uma aplicação PHP/Apache e configurar o Horizontal Pod Autoscaler (HPA) para escalabilidade automática baseada no uso de CPU.

## Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/)
- [kubectl](https://kubernetes.io/docs/tasks/tools/install-kubectl/)
- [kind](https://kind.sigs.k8s.io/docs/user/quick-start/#installation)

## Estrutura do Repositório

```
.
├── k8s/
│   ├── deployment-service.yaml
│   └── hpa.yaml
├── app/
│   ├── Dockerfile
│   └── src.php
└── README.md
```

## Passo a Passo para Execução

### 1. Criar o Cluster Kubernetes com Kind


```bash
kind create cluster --name hpa-cluster
```

### 2. Construir e Carregar a Imagem Docker

(Comando tem que ser rodado no root do repositorio)

```bash
docker build -t php-apache:ponderada ./php-apache/
kind load docker-image php-apache:ponderada --name hpa-cluster
```

### 3. Instalar o Metrics Server

O HPA precisa do Metrics Server para coletar as métricas de uso de CPU e memória dos pods.

```bash
kubectl apply -f [https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml](https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml)
```

### 4. Aplicar os Arquivos Kubernetes

Aplique os arquivos de configuração do Deployment, Service e HPA no cluster.

```bash
kubectl apply -f ./k8s/
```

### 5. Verificar a Configuração

Verifique se os componentes foram criados corretamente.

```bash
# Verifica o pod em execução
kubectl get pods

# Verifica o HPA
kubectl get hpa

# Verifica o serviço
kubectl get svc
```

## Teste de Carga e Análise de Métricas

### 1. Monitoramento

Em um **novo terminal**, use o comando abaixo para observar o HPA em tempo real. Ele será atualizado automaticamente.

```bash
kubectl get hpa -w
```

Em **outro terminal**, observe os pods sendo criados:

```bash
kubectl get pods -w
```

### 2. Teste de Carga

Inicialmente eu tentei rodar o teste de carga utilizando kubectl port-forward e múltiplos loops de curl mas esse método se mostrou bastante ineficaz, O gargalo do port-forward e a alta capacidade de processamento da minha máquina (i7) impediram que a CPU do pod atingisse o limiar de 50% necessário para acionar o HPA, mesmo com vários terminais gerando requisições.

Pra isso, eu troquei pro Nodeport (Que elimina a necessidade de um forward) e utilizei o ApacheBench que é muito profissional em obliterar minha CPU.

Seguem etapas:

#### 1. Pegar porta do Nodeport
utilize o comando ```kubectl get svc php-apache-service``` pra pegar a sua porta

#### 2. Pegar IP do cluster Kind
utilize o comando ```docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' hpa-cluster-control-plane``` pra pegar o IP do seu cluster

#### 3. Ligue o ObliteradorDeCPUs2000 (ApacheBench)
utilize o comando ```ab -c 100 -t 120 http://IP_DO_CLUSTER:SUA_PORTA/```




