import { SyncService, SCHEMA_VERSION } from '@/services/SyncService'
import { db } from '@/db/database'
import type { Account, Deletion } from '@/types'

vi.mock('@/db/database', () => ({
  db: {
    accounts:     { toArray: vi.fn(), bulkPut: vi.fn(), bulkDelete: vi.fn() },
    budgets:      { toArray: vi.fn(), bulkPut: vi.fn(), bulkDelete: vi.fn() },
    transactions: { toArray: vi.fn(), bulkPut: vi.fn(), bulkDelete: vi.fn() },
    deletions:    { toArray: vi.fn(), clear: vi.fn() },
    // Appelle le dernier argument (la callback async) et retourne sa promesse
    transaction:  vi.fn((...args: unknown[]) => (args[args.length - 1] as () => unknown)()),
  }
}))

function makeAccount(overrides: Partial<Account> = {}): Account {
  return {
    id: 'a1', label: 'Compte courant', type: 'checking',
    balance: 0, color: '#6366F1', currency: 'EUR',
    createdAt: '2026-01-01T00:00:00Z', updatedAt: '2026-01-01T00:00:00Z',
    ...overrides,
  }
}

const EMPTY_DUMP = {
  schemaVersion: SCHEMA_VERSION,
  accounts: [], budgets: [], transactions: [], deletions: [],
}

// ─────────────────────────────────────────────────────────────────────────────

describe('SyncService.mergeDump', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    vi.mocked(db.accounts.toArray).mockResolvedValue([])
    vi.mocked(db.budgets.toArray).mockResolvedValue([])
    vi.mocked(db.transactions.toArray).mockResolvedValue([])
  })

  it('rejects a dump with a mismatched schema version', async () => {
    const result = await SyncService.mergeDump({ ...EMPTY_DUMP, schemaVersion: 99 })
    expect(result.ok).toBe(false)
    expect(result.error).toContain('schema_mismatch')
  })

  it('upserts a record not present locally', async () => {
    const remote = makeAccount({ id: 'new' })
    const result = await SyncService.mergeDump({ ...EMPTY_DUMP, accounts: [remote] })
    expect(result.ok).toBe(true)
    expect(vi.mocked(db.accounts.bulkPut)).toHaveBeenCalledWith([remote])
  })

  it('overwrites a local record when the remote one is newer', async () => {
    const local  = makeAccount({ updatedAt: '2026-01-01T00:00:00Z' })
    const remote = makeAccount({ updatedAt: '2026-06-01T00:00:00Z' })
    vi.mocked(db.accounts.toArray).mockResolvedValue([local])

    const result = await SyncService.mergeDump({ ...EMPTY_DUMP, accounts: [remote] })
    expect(result.ok).toBe(true)
    expect(vi.mocked(db.accounts.bulkPut)).toHaveBeenCalledWith([remote])
  })

  it('skips a remote record older than the local one', async () => {
    const local  = makeAccount({ updatedAt: '2026-06-01T00:00:00Z' })
    const remote = makeAccount({ updatedAt: '2026-01-01T00:00:00Z' })
    vi.mocked(db.accounts.toArray).mockResolvedValue([local])

    const result = await SyncService.mergeDump({ ...EMPTY_DUMP, accounts: [remote] })
    expect(result.ok).toBe(true)
    expect(vi.mocked(db.accounts.bulkPut)).not.toHaveBeenCalled()
  })

  it('deletes records listed in remote deletions', async () => {
    const deletion: Deletion = {
      id: 'd1', tableName: 'accounts' as const, recordId: 'a1', deletedAt: '2026-01-01T00:00:00Z',
    }
    const result = await SyncService.mergeDump({ ...EMPTY_DUMP, deletions: [deletion] })
    expect(result.ok).toBe(true)
    expect(vi.mocked(db.accounts.bulkDelete)).toHaveBeenCalledWith(['a1'])
  })

  it('returns ok:true on a valid empty dump', async () => {
    const result = await SyncService.mergeDump(EMPTY_DUMP)
    expect(result.ok).toBe(true)
  })
})

// ─────────────────────────────────────────────────────────────────────────────

describe('SyncService.exportDump', () => {
  it('returns all records with the correct schema version', async () => {
    const account = makeAccount()
    vi.mocked(db.accounts.toArray).mockResolvedValue([account])
    vi.mocked(db.budgets.toArray).mockResolvedValue([])
    vi.mocked(db.transactions.toArray).mockResolvedValue([])
    vi.mocked(db.deletions.toArray).mockResolvedValue([])

    const dump = await SyncService.exportDump()

    expect(dump.schemaVersion).toBe(SCHEMA_VERSION)
    expect(dump.accounts).toEqual([account])
    expect(dump.budgets).toEqual([])
    expect(dump.transactions).toEqual([])
  })
})

// ─────────────────────────────────────────────────────────────────────────────

describe('SyncService.clearDeletions', () => {
  it('clears the deletions table', async () => {
    await SyncService.clearDeletions()
    expect(vi.mocked(db.deletions.clear)).toHaveBeenCalledOnce()
  })
})
