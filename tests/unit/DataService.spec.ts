import { clearAllData, importData, exportData } from '@/services/DataService'
import { db } from '@/db/database'
import { SyncService } from '@/services/SyncService'

vi.mock('@/db/database', () => ({
  db: {
    accounts:     { clear: vi.fn() },
    budgets:      { clear: vi.fn() },
    transactions: { clear: vi.fn() },
    deletions:    { clear: vi.fn() },
    transaction:  vi.fn((...args: unknown[]) => (args[args.length - 1] as () => unknown)()),
  }
}))

vi.mock('@/services/SyncService', () => ({
  SyncService: {
    exportDump: vi.fn(),
    mergeDump:  vi.fn(),
  },
}))

const VALID_DUMP = {
  schemaVersion: 3,
  accounts: [], budgets: [], transactions: [], deletions: [],
}

// File.prototype.text() n'est pas implémenté dans jsdom — on l'injecte manuellement
function makeJsonFile(obj: unknown, name = 'backup.json'): File {
  const content = JSON.stringify(obj)
  const file = new File([content], name, { type: 'application/json' })
  Object.defineProperty(file, 'text', { value: () => Promise.resolve(content) })
  return file
}

// ─────────────────────────────────────────────────────────────────────────────

describe('clearAllData', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    vi.mocked(db.accounts.clear).mockResolvedValue(undefined)
    vi.mocked(db.budgets.clear).mockResolvedValue(undefined)
    vi.mocked(db.transactions.clear).mockResolvedValue(undefined)
    vi.mocked(db.deletions.clear).mockResolvedValue(undefined)
  })

  it('clears all four tables', async () => {
    await clearAllData()
    expect(vi.mocked(db.accounts.clear)).toHaveBeenCalledOnce()
    expect(vi.mocked(db.budgets.clear)).toHaveBeenCalledOnce()
    expect(vi.mocked(db.transactions.clear)).toHaveBeenCalledOnce()
    expect(vi.mocked(db.deletions.clear)).toHaveBeenCalledOnce()
  })
})

// ─────────────────────────────────────────────────────────────────────────────

describe('importData', () => {
  beforeEach(() => vi.clearAllMocks())

  it('throws on an invalid JSON structure', async () => {
    await expect(importData(makeJsonFile({ foo: 'bar' }))).rejects.toThrow('Invalid backup file')
  })

  it('propagates the error returned by mergeDump', async () => {
    vi.mocked(SyncService.mergeDump).mockResolvedValue({
      ok: false, error: 'schema_mismatch:local=3,remote=2',
    })
    await expect(importData(makeJsonFile(VALID_DUMP))).rejects.toThrow('schema_mismatch')
  })

  it('resolves on a valid dump', async () => {
    vi.mocked(SyncService.mergeDump).mockResolvedValue({ ok: true })
    await expect(importData(makeJsonFile(VALID_DUMP))).resolves.toBeUndefined()
    expect(SyncService.mergeDump).toHaveBeenCalledWith(VALID_DUMP)
  })
})

// ─────────────────────────────────────────────────────────────────────────────

describe('exportData', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    global.URL.createObjectURL = vi.fn(() => 'blob:mock')
    global.URL.revokeObjectURL = vi.fn()
  })

  it('calls exportDump and triggers a file download', async () => {
    vi.mocked(SyncService.exportDump).mockResolvedValue(VALID_DUMP)
    const clickSpy = vi.spyOn(HTMLAnchorElement.prototype, 'click').mockImplementation(() => {})

    await exportData()

    expect(SyncService.exportDump).toHaveBeenCalledOnce()
    expect(clickSpy).toHaveBeenCalledOnce()
  })

  it('names the file with today\'s date', async () => {
    vi.mocked(SyncService.exportDump).mockResolvedValue(VALID_DUMP)
    vi.spyOn(HTMLAnchorElement.prototype, 'click').mockImplementation(() => {})
    const setSpy = vi.spyOn(HTMLAnchorElement.prototype, 'download', 'set')

    await exportData()

    const today = new Date().toISOString().slice(0, 10)
    expect(setSpy).toHaveBeenCalledWith(expect.stringContaining(today))
  })
})
