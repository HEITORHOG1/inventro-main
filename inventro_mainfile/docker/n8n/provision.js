/**
 * Inventro n8n — Auto-provisioning
 *
 * Roda em background apos o n8n iniciar.
 * Cria owner, importa workflows e ativa o workflow de teste.
 *
 * Usa apenas modulos nativos do Node.js (sem dependencias externas).
 */

const http = require('http');
const fs = require('fs');
const path = require('path');

const N8N_BASE = 'http://localhost:5678';
const WORKFLOWS_DIR = '/home/node/workflows';
const MAX_WAIT_S = 60;
const OWNER_EMAIL = process.env.N8N_OWNER_EMAIL || 'admin@inventro.com';
const OWNER_PASSWORD = process.env.N8N_OWNER_PASSWORD || 'Inventro2026';
const OWNER_FIRST = process.env.N8N_OWNER_FIRST || 'Inventro';
const OWNER_LAST = process.env.N8N_OWNER_LAST || 'Admin';

let sessionCookie = '';

// ─── Helpers ──────────────────────────────────────────────

function log(msg) {
  console.log(`[n8n-provision] ${msg}`);
}

function logError(msg) {
  console.error(`[n8n-provision] ERROR: ${msg}`);
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * HTTP request usando http nativo. Retorna { statusCode, headers, body }.
 */
function request(method, urlPath, body, extraHeaders) {
  return new Promise((resolve, reject) => {
    const url = new URL(urlPath, N8N_BASE);
    const payload = body ? JSON.stringify(body) : null;

    const headers = {
      'Content-Type': 'application/json',
      ...extraHeaders,
    };
    if (payload) {
      headers['Content-Length'] = Buffer.byteLength(payload);
    }
    if (sessionCookie) {
      headers['Cookie'] = sessionCookie;
    }

    const req = http.request(
      {
        hostname: url.hostname,
        port: url.port,
        path: url.pathname + url.search,
        method,
        headers,
        timeout: 10000,
      },
      (res) => {
        const chunks = [];
        res.on('data', (chunk) => chunks.push(chunk));
        res.on('end', () => {
          const rawBody = Buffer.concat(chunks).toString();
          let parsed = null;
          try {
            parsed = JSON.parse(rawBody);
          } catch (_) {
            parsed = rawBody;
          }

          // Captura cookie de sessao do login
          const setCookie = res.headers['set-cookie'];
          if (setCookie) {
            const cookies = setCookie.map(c => c.split(';')[0]).join('; ');
            if (cookies) sessionCookie = cookies;
          }

          resolve({ statusCode: res.statusCode, headers: res.headers, body: parsed });
        });
      }
    );

    req.on('error', reject);
    req.on('timeout', () => { req.destroy(); reject(new Error('Request timeout')); });

    if (payload) req.write(payload);
    req.end();
  });
}

// ─── Steps ────────────────────────────────────────────────

async function waitForN8n() {
  log('Aguardando n8n ficar pronto...');
  const start = Date.now();

  while ((Date.now() - start) < MAX_WAIT_S * 1000) {
    try {
      const res = await request('GET', '/healthz');
      if (res.statusCode === 200) {
        log('n8n pronto!');
        return true;
      }
    } catch (_) {
      // n8n ainda nao respondeu
    }
    await sleep(2000);
  }

  logError(`n8n nao respondeu em ${MAX_WAIT_S}s`);
  return false;
}

async function setupOwner() {
  log('Verificando se owner ja existe...');

  // Tenta login primeiro — se funcionar, owner ja existe
  try {
    const loginRes = await request('POST', '/rest/login', {
      emailOrLdapLoginId: OWNER_EMAIL,
      password: OWNER_PASSWORD,
    });

    if (loginRes.statusCode === 200 && loginRes.body && loginRes.body.data) {
      log(`Owner ja existe: ${OWNER_EMAIL} (login OK)`);
      return true;
    }
  } catch (_) {
    // Login falhou, owner pode nao existir
  }

  // Tenta criar owner via setup
  log(`Criando owner: ${OWNER_EMAIL}...`);
  try {
    const setupRes = await request('POST', '/rest/owner/setup', {
      email: OWNER_EMAIL,
      password: OWNER_PASSWORD,
      firstName: OWNER_FIRST,
      lastName: OWNER_LAST,
    });

    if (setupRes.statusCode === 200 || setupRes.statusCode === 201) {
      log('Owner criado com sucesso!');
      // Login apos setup para obter sessao
      const loginRes = await request('POST', '/rest/login', {
        emailOrLdapLoginId: OWNER_EMAIL,
        password: OWNER_PASSWORD,
      });
      return loginRes.statusCode === 200;
    }

    // Ja configurado (setup ja foi feito antes)
    if (setupRes.statusCode === 400 || setupRes.statusCode === 409) {
      log('Setup ja foi realizado anteriormente. Tentando login...');
      const loginRes = await request('POST', '/rest/login', {
        emailOrLdapLoginId: OWNER_EMAIL,
        password: OWNER_PASSWORD,
      });
      if (loginRes.statusCode === 200) {
        log('Login OK!');
        return true;
      }
      logError(`Login falhou apos setup: HTTP ${loginRes.statusCode}`);
      return false;
    }

    logError(`Setup falhou: HTTP ${setupRes.statusCode} — ${JSON.stringify(setupRes.body)}`);
    return false;
  } catch (err) {
    logError(`Setup erro: ${err.message}`);
    return false;
  }
}

async function listExistingWorkflows() {
  try {
    const res = await request('GET', '/rest/workflows');
    if (res.statusCode === 200) {
      const wfs = (res.body.data || res.body || []);
      return wfs;
    }
  } catch (_) {}
  return [];
}

function loadWorkflowFiles() {
  const files = [];
  try {
    const entries = fs.readdirSync(WORKFLOWS_DIR);
    for (const entry of entries) {
      if (!entry.endsWith('.json')) continue;
      const filePath = path.join(WORKFLOWS_DIR, entry);
      try {
        const content = JSON.parse(fs.readFileSync(filePath, 'utf8'));
        files.push({ filename: entry, content });
      } catch (err) {
        logError(`Erro ao ler ${entry}: ${err.message}`);
      }
    }
  } catch (err) {
    logError(`Erro ao listar ${WORKFLOWS_DIR}: ${err.message}`);
  }
  return files;
}

async function importWorkflows() {
  const existing = await listExistingWorkflows();
  const existingByName = {};
  for (const wf of existing) {
    existingByName[wf.name] = wf;
  }

  const files = loadWorkflowFiles();
  if (files.length === 0) {
    logError('Nenhum workflow JSON encontrado');
    return [];
  }

  log(`Encontrados ${files.length} arquivos de workflow`);
  const imported = [];

  for (const { filename, content } of files) {
    const name = content.name || filename;
    // Remove campos que o n8n gera internamente ou que causam erro na API
    const { id: _id, tags: _tags, ...workflowData } = content;

    try {
      if (existingByName[name]) {
        // Atualizar existente
        const existingWf = existingByName[name];
        const updatePayload = {
          ...workflowData,
          versionId: existingWf.versionId,
        };
        const res = await request('PATCH', `/rest/workflows/${existingWf.id}`, updatePayload);
        if (res.statusCode === 200) {
          const wf = res.body.data || res.body;
          log(`  ✓ Atualizado: ${name} (id: ${wf.id})`);
          imported.push({ name, id: wf.id, versionId: wf.versionId, action: 'updated' });
        } else {
          logError(`  ✗ Falha ao atualizar ${name}: HTTP ${res.statusCode}`);
        }
      } else {
        // Criar novo
        const res = await request('POST', '/rest/workflows', workflowData);
        if (res.statusCode === 200 || res.statusCode === 201) {
          const wf = res.body.data || res.body;
          log(`  ✓ Criado: ${name} (id: ${wf.id})`);
          imported.push({ name, id: wf.id, versionId: wf.versionId, action: 'created' });
        } else {
          logError(`  ✗ Falha ao criar ${name}: HTTP ${res.statusCode} — ${JSON.stringify(res.body)}`);
        }
      }
    } catch (err) {
      logError(`  ✗ Erro em ${name}: ${err.message}`);
    }
  }

  return imported;
}

async function activateTestWorkflow(imported) {
  // Encontra workflow 06 (teste) pelo nome
  const testWf = imported.find(w => w.name && w.name.includes('Teste'));

  if (!testWf) {
    // Busca na lista de workflows existentes
    const all = await listExistingWorkflows();
    const found = all.find(w => w.name && w.name.includes('Teste'));
    if (!found) {
      log('Workflow de teste nao encontrado — pulando ativacao');
      return;
    }
    // Ja pode estar ativo
    if (found.active) {
      log(`Workflow de teste ja esta ativo (id: ${found.id})`);
      return;
    }

    try {
      // Precisa do versionId completo
      const detailRes = await request('GET', `/rest/workflows/${found.id}`);
      const detail = detailRes.body.data || detailRes.body;

      const res = await request('POST', `/rest/workflows/${found.id}/activate`, {
        versionId: detail.versionId,
      });
      const wf = res.body.data || res.body;
      log(`Workflow de teste ativado: ${wf.active ? 'OK' : 'falhou'} (id: ${found.id})`);
    } catch (err) {
      logError(`Erro ao ativar workflow de teste: ${err.message}`);
    }
    return;
  }

  try {
    const res = await request('POST', `/rest/workflows/${testWf.id}/activate`, {
      versionId: testWf.versionId,
    });
    const wf = res.body.data || res.body;
    log(`Workflow de teste ativado: ${wf.active ? 'OK' : 'falhou'} (id: ${testWf.id})`);
  } catch (err) {
    logError(`Erro ao ativar workflow de teste: ${err.message}`);
  }
}

// ─── Main ─────────────────────────────────────────────────

async function main() {
  log('Iniciando provisionamento...');

  // 1. Aguardar n8n
  const ready = await waitForN8n();
  if (!ready) {
    logError('Abortando — n8n nao esta pronto');
    process.exit(0); // Exit 0 para nao afetar o container
  }

  // 2. Setup owner + login
  const loggedIn = await setupOwner();
  if (!loggedIn) {
    logError('Abortando — nao foi possivel autenticar');
    process.exit(0);
  }

  // 3. Importar workflows
  const imported = await importWorkflows();
  log(`${imported.length} workflow(s) provisionado(s)`);

  // 4. Ativar workflow de teste
  await activateTestWorkflow(imported);

  log('Provisionamento concluido!');
}

main().catch((err) => {
  logError(`Erro fatal: ${err.message}`);
  process.exit(0);
});
