# 💎 Identidade Visual Premium - Esmalteria Vida Maria

## 🎨 Paleta de Cores

### Azul Marinho (Navy)
- **Principal**: `#0A1647` - Elegância, sofisticação, confiança
- **Uso**: Backgrounds principais, headers, textos importantes

### Dourado (Gold)  
- **Principal**: `#D4AF37` - Luxo, premium, exclusividade
- **Uso**: Destaques, botões principais, bordas, ícones especiais

### Cores Complementares
- **Branco**: `#FFFFFF` - Limpeza, clareza
- **Cinza Claro**: `#F5F5F5` - Backgrounds suaves

---

## 📐 Classes CSS Premium Disponíveis

### Botões
```html
<!-- Botão Dourado Principal -->
<button class="btn-primary">Agendar Agora</button>

<!-- Botão Azul Secundário -->
<button class="btn-secondary">Ver Mais</button>
```

### Cards
```html
<!-- Card Premium com borda dourada -->
<div class="card-premium">
    Conteúdo do card
</div>
```

### Gradientes
```html
<!-- Gradiente Azul Marinho -->
<div class="gradient-navy-gold">
    Header Premium
</div>

<!-- Gradiente Dourado -->
<div class="gradient-gold">
    Destaque especial
</div>
```

### Texto Premium
```html
<!-- Texto com gradiente dourado -->
<h1 class="text-premium">Vida Maria</h1>
```

### Badges
```html
<!-- Badge Dourado -->
<span class="badge-gold">Premium</span>

<!-- Badge Azul -->
<span class="badge-navy">Concluído</span>
```

---

## 🎯 Uso nas Páginas

### Dashboard
- **Header**: Gradiente navy-gold
- **Cards de Total**: Gradiente gold para empresa, blue para profissionais
- **Badges de Status**: Gold para concluído, Navy para agendado

### Navegação
- **Background**: Azul marinho escuro
- **Links Ativos**: Dourado
- **Links Inativos**: Branco, hover dourado claro

### Botões de Ação
- **Criar/Salvar**: Dourado (btn-primary)
- **Cancelar**: Cinza
- **Deletar**: Vermelho
- **Ver/Editar**: Azul marinho (btn-secondary)

---

## ✨ Efeitos Premium

### Hover Effects
- Todos os botões têm `hover:scale-105` (crescem 5%)
- Sombras aumentam no hover
- Transições suaves de 300ms

### Shadows
- Cards: `shadow-lg` → `hover:shadow-2xl`
- Botões: `shadow-lg` → `hover:shadow-xl`

### Bordas
- Cards premium: Borda superior dourada de 4px
- Navegação: Borda inferior dourada de 4px

---

## 🎨 Cores Tailwind Customizadas

### Navy (Azul Marinho)
```
vm-navy-50   - Muito claro (backgrounds)
vm-navy-100
vm-navy-200
vm-navy-300
vm-navy-400
vm-navy-500
vm-navy-600
vm-navy-700
vm-navy-800  - Principal (#0A1647)
vm-navy-900  - Muito escuro
```

### Gold (Dourado)
```
vm-gold-50   - Muito claro
vm-gold-100
vm-gold-200
vm-gold-300
vm-gold-400
vm-gold-500  - Principal (#D4AF37)
vm-gold-600
vm-gold-700
vm-gold-800
vm-gold-900  - Muito escuro
```

---

## 📱 Responsividade

Todas as classes mantêm o visual premium em:
- 📱 Mobile (< 640px)
- 📱 Tablet (640px - 1024px)
- 💻 Desktop (> 1024px)

---

## 🚀 Como Usar

### Em Blade Templates
```blade
<!-- Card Premium -->
<div class="card-premium">
    <h3 class="text-premium text-2xl mb-4">Título Dourado</h3>
    <p class="text-vm-navy-700">Texto em azul marinho</p>
    <button class="btn-primary mt-4">Ação Principal</button>
</div>
```

### Backgrounds
```blade
<!-- Fundo azul marinho -->
<div class="bg-vm-navy-800 text-white p-6">
    Conteúdo
</div>

<!-- Fundo dourado -->
<div class="bg-vm-gold-100 text-vm-navy-800 p-6">
    Destaque
</div>
```

---

## 💡 Dicas de Design Premium

1. **Contraste**: Sempre use texto escuro em fundos claros e vice-versa
2. **Hierarquia**: Use dourado para destaques importantes (CTAs, valores)
3. **Espaçamento**: Mantenha bastante espaço em branco
4. **Tipografia**: Textos importantes em negrito
5. **Shadows**: Use sombras para criar profundidade
6. **Hover**: Sempre adicione feedback visual ao hover

---

## 🎯 Padrão de Cores por Contexto

| Contexto | Cor Principal | Uso |
|----------|---------------|-----|
| Sucesso | Verde | Confirmações |
| Atenção | Laranja | Avisos |
| Erro | Vermelho | Erros, deletar |
| Info | Azul Navy | Informações |
| Premium | Dourado | Destaques, valores |

---

## 🔄 Recompilar CSS

Sempre que alterar cores ou adicionar classes:

```bash
npm run build
```

Ou em desenvolvimento:
```bash
npm run dev
```

---

**Criado para Esmalteria Vida Maria** ✨💅
Mantendo o padrão premium e elegante!

