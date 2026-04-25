<template>
  <!-- TnsList — rounded-corner container for grouped rows.
       Slot accepts any mix of TnsTransactionRow, TnsAccountCard… -->
  <div class="tns-list"><slot/></div>
</template>

<style scoped>
.tns-list {
  background: var(--tns-card);
  border-radius: var(--tns-radius-lg);
  margin: 0 16px 14px;
  overflow: hidden;
}
</style>
