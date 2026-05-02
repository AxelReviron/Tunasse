import { db } from '@/db/database'
import { SyncService, type SyncDump } from '@/services/SyncService'

export async function exportData(): Promise<void> {
  const dump = await SyncService.exportDump()
  const payload = { ...dump, exportedAt: new Date().toISOString() }

  const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `tunasse-backup-${new Date().toISOString().slice(0, 10)}.json`
  a.click()
  URL.revokeObjectURL(url)
}

export async function clearAllData(): Promise<void> {
  await db.transaction('rw', [db.accounts, db.budgets, db.transactions, db.deletions], async () => {
    await Promise.all([
      db.accounts.clear(),
      db.budgets.clear(),
      db.transactions.clear(),
      db.deletions.clear(),
    ])
  })
}

export async function importData(file: File): Promise<void> {
  const text = await file.text()
  const payload = JSON.parse(text) as SyncDump

  if (typeof payload !== 'object' || !Array.isArray(payload.accounts) || !Array.isArray(payload.transactions)) {
    throw new Error('Invalid backup file')
  }

  const result = await SyncService.mergeDump(payload)
  if (!result.ok) throw new Error(result.error ?? 'merge_failed')
}
