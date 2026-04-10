<script setup lang="ts">
import { Position } from '@unovis/ts'
import { VisAxis, VisCrosshair, VisStackedBar, VisTooltip, VisXYContainer } from '@unovis/vue'
import {defineProps, ref, watch} from "vue";
import {PrisonerRecord} from "@/@types/types";
import {useChart} from "@/composables/useChart";

interface Props {
  records: PrisonerRecord[]
}

const props = defineProps<Props>()
type FormatConfig = { format: any, color: string, icon: string, label: string  }
type TDataKeys = 'race' | 'gender' | 'ideology' | 'affiliation'
const chartKeys = ['race', 'gender', 'ideology', 'affiliation']
const activeFilterKey = ref('race')
const { initialiseChartData, prepareData, generateLabels, transformData } = useChart()
const height = 450

initialiseChartData(props.records)

const preparedData = ref({
  race: prepareData('race'),
  gender: prepareData('gender'),
  ideology: prepareData('ideology'),
  affiliation: prepareData('affiliation'),
})

const labels = ref<any>(generateLabels(preparedData.value.race.series))
const chartData = ref<any>(transformData(preparedData.value.race))

watch(() => props.records, (newRecords, oldRecords) => {
  initialiseChartData(props.records)

  preparedData.value = {
    race: prepareData('race'),
    gender: prepareData('gender'),
    ideology: prepareData('ideology'),
    affiliation: prepareData('affiliation'),
  }

  const key = activeFilterKey.value ?? 'race'
   labels.value = generateLabels(preparedData.value[key].series)
   chartData.value = transformData(preparedData.value[key])
}, { deep: true });


function setChartData(key: TDataKeys) {
  activeFilterKey.value = key
  labels.value = generateLabels(preparedData.value[key].series)
  chartData.value = transformData(preparedData.value[key])
}


function getIcon(f: any): string {
  return `<span class="bi bi-${f.icon}" style="background:${f.color}; color:${f.color};margin:0 4px;position: relative;top:2px; height: 16px;overflow: hidden;border-radius: 3px;display: inline-block;"></span>${f.label}\t`
}

function tooltipTemplate(d: Record<string, number>): string {
  let total = 0
  Object.keys(d).forEach((key: string) => {
    if(key.toLocaleLowerCase() !== 'year') total += d[key]
  })

  const dataLegend = labels.value.filter((f: FormatConfig) => d[f.format] > 0)
      .reverse()
      .map((f: FormatConfig) => `<div><div style="margin-top: .35rem">${getIcon({ ...f, label: d[f.format] })} ${f.label}</div>`)
      .join('</div>')
  return `<div><b>${d.year} | Total ${total}</b> ${dataLegend}</div>`
}
</script>

<template>
 <section class="bg-black" id="graph-component">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css" />

   <nav class="bg-black">
     <div class="container flex justify-between pb-6">
       <div
           v-for="key  in chartKeys"
           @click="() => setChartData(key as TDataKeys)"
           :key="key"
           :class="activeFilterKey === key ? 'app-color-bg app-color-border-light' : 'border-white '"
           class="text-white flex-grow text-center border capitalize px-6 py-4 cursor-pointer"
       >
         <span>{{key}}</span>
       </div>
     </div>
   </nav>

   <div style="height:100px;margin-top: 2rem;">
     <template v-for="(l, index) in labels">
     <span v-if="index < 10" class="mr-3 mb-3 text-white" v-html="getIcon(l)"></span>
     </template>
   </div>

   <VisXYContainer :data="chartData" :height="height">
     <VisStackedBar :barPadding="0.05"  :x="(d: any) => d.year" :y="labels.map((l: any) => (d: any) => d[l.format])" />
     <VisCrosshair :template="tooltipTemplate" />
     <VisTooltip :verticalShift="height" :horizontalPlacement="Position.Center" />
     <VisAxis type="x" :numTicks="20" />
     <VisAxis type='y' label='Total Political Prisoners' />
   </VisXYContainer>
 </section>
</template>


<style lang="scss">
tspan {
  fill: #FFF;
}

#graph-component {

  svg {

    text {
      fill: #FFF;
    }
  }
}
</style>
