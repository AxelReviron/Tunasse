import { ref, computed, onUnmounted } from 'vue'
import { liveQuery } from 'dexie'
import { AccountService, type BalanceEntry } from '@/services/AccountService'
import type { Account } from '@/types'

export function useAccounts() {
  const accounts      = ref<Account[]>([])
  const totalBalance  = ref<BalanceEntry[]>([])
  const isLoading     = ref(true)

  const subAccounts = liveQuery(() => AccountService.getAll())
    .subscribe({
      next:  result => { accounts.value = result; isLoading.value = false },
      error: err    => { console.error(err);       isLoading.value = false },
    })

  const subBalance = liveQuery(() => AccountService.getTotalBalance())
    .subscribe({ next: val => { totalBalance.value = val } })

  onUnmounted(() => {
    subAccounts.unsubscribe()
    subBalance.unsubscribe()
  })

  function getById(id: number): Account | undefined {
    return accounts.value.find(a => a.id === id)
  }

  async function create(account: Omit<Account, 'id'>) {
    return AccountService.create(account)
  }

  async function update(id: number, changes: Partial<Omit<Account, 'id'>>) {
    return AccountService.update(id, changes)
  }

  async function remove(id: number) {
    return AccountService.remove(id)
  }

  return { accounts, totalBalance, isLoading, getById, create, update, remove }
}
