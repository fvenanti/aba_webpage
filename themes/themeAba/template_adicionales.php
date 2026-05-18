<?php /* Template Name: Adicionales */ get_header(); ?>
<style>
.adicionales-section { padding: 48px 0 64px; }
.adicionales-intro { text-align: center; margin-bottom: 48px; }
.adicionales-intro p { color: #718096; font-size: 16px; margin-top: 8px; }
.adicionales-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 40px 24px;
}
.adicional-item { text-align: center; }
.adicional-icon {
  width: 76px; height: 76px;
  border-radius: 50%;
  border: 2px solid #679938;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px;
}
.adicional-icon svg { width: 34px; height: 34px; stroke: #679938; fill: none; stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round; }
.adicional-name { font-weight: bold; font-size: 15px; color: #1A202C; margin-bottom: 8px; line-height: 1.4; }
.adicional-desc { font-size: 13px; color: #718096; line-height: 1.6; }
.adicionales-nota { text-align: center; margin-top: 48px; font-size: 13px; color: #A0AEC0; font-style: italic; }
@media (max-width: 900px) {
  .adicionales-grid { grid-template-columns: repeat(2, 1fr); gap: 32px 20px; }
}
@media (max-width: 480px) {
  .adicionales-grid { grid-template-columns: repeat(2, 1fr); gap: 24px 12px; }
  .adicional-icon { width: 64px; height: 64px; }
  .adicional-icon svg { width: 28px; height: 28px; }
}
</style>

<div class="titlePage">
  <div class="box">
    <h1><?php the_title(); ?></h1>
  </div>
</div>

<div class="adicionales-section">
  <div class="box">

    <div class="adicionales-intro">
      <?php if (qtranxf_getLanguage() == 'en'): ?>
        <p>Services available upon request</p>
      <?php elseif (qtranxf_getLanguage() == 'pt'): ?>
        <p>Serviços disponíveis mediante solicitação</p>
      <?php else: ?>
        <p>Servicios con cargo y previa solicitud</p>
      <?php endif; ?>
    </div>

    <div class="adicionales-grid">

      <!-- Entrega/devolución fuera de horario -->
      <div class="adicional-item">
        <div class="adicional-icon">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <?php if (qtranxf_getLanguage() == 'en'): ?>
          <div class="adicional-name">After-hours delivery/return</div>
          <div class="adicional-desc">Vehicle delivery or return outside regular office hours (after 9 PM and before 7 AM).</div>
        <?php elseif (qtranxf_getLanguage() == 'pt'): ?>
          <div class="adicional-name">Entrega/devolução fora do horário</div>
          <div class="adicional-desc">Entrega ou devolução do veículo fora do horário de atendimento (após as 21:00 e até as 07:00 hs).</div>
        <?php else: ?>
          <div class="adicional-name">Entrega/devolución fuera de horario</div>
          <div class="adicional-desc">Servicio disponible fuera del horario de atención habitual (luego de las 21:00 y hasta las 07:00 hs).</div>
        <?php endif; ?>
      </div>

      <!-- GPS -->
      <div class="adicional-item">
        <div class="adicional-icon">
          <svg viewBox="0 0 24 24"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
        </div>
        <?php if (qtranxf_getLanguage() == 'en'): ?>
          <div class="adicional-name">GPS</div>
          <div class="adicional-desc">GPS navigation device to help you explore Patagonia without getting lost.</div>
        <?php elseif (qtranxf_getLanguage() == 'pt'): ?>
          <div class="adicional-name">GPS</div>
          <div class="adicional-desc">Dispositivo de navegação GPS para explorar a Patagônia com tranquilidade.</div>
        <?php else: ?>
          <div class="adicional-name">GPS</div>
          <div class="adicional-desc">Dispositivo de navegación GPS para recorrer la Patagonia sin perderte.</div>
        <?php endif; ?>
      </div>

      <!-- Cadenas para hielo y nieve -->
      <div class="adicional-item">
        <div class="adicional-icon">
          <svg viewBox="0 0 24 24"><line x1="12" y1="2" x2="12" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/><line x1="19.07" y1="4.93" x2="4.93" y2="19.07"/></svg>
        </div>
        <?php if (qtranxf_getLanguage() == 'en'): ?>
          <div class="adicional-name">Snow & Ice Chains</div>
          <div class="adicional-desc">Essential for winter driving in Patagonia. We recommend them for travel in snowy conditions.</div>
        <?php elseif (qtranxf_getLanguage() == 'pt'): ?>
          <div class="adicional-name">Correntes para gelo e neve</div>
          <div class="adicional-desc">Indispensáveis para dirigir na Patagônia no inverno. Recomendamos para viagens em condições de neve.</div>
        <?php else: ?>
          <div class="adicional-name">Cadenas para hielo y nieve</div>
          <div class="adicional-desc">Indispensables para conducir en la Patagonia en invierno. Las recomendamos para viajes en condiciones de nieve.</div>
        <?php endif; ?>
      </div>

      <!-- Barras portaequipaje -->
      <div class="adicional-item">
        <div class="adicional-icon">
          <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="3" rx="1"/><rect x="4" y="10" width="16" height="7" rx="1"/><line x1="7" y1="10" x2="7" y2="17"/><line x1="12" y1="10" x2="12" y2="17"/><line x1="17" y1="10" x2="17" y2="17"/><line x1="5" y1="7" x2="5" y2="4"/><line x1="19" y1="7" x2="19" y2="4"/></svg>
        </div>
        <?php if (qtranxf_getLanguage() == 'en'): ?>
          <div class="adicional-name">Roof Rack Bars</div>
          <div class="adicional-desc">Ideal for carrying extra luggage, bicycles, or equipment on your adventure.</div>
        <?php elseif (qtranxf_getLanguage() == 'pt'): ?>
          <div class="adicional-name">Barras de teto</div>
          <div class="adicional-desc">Ideais para transportar bagagem extra, bicicletas ou equipamentos na sua aventura.</div>
        <?php else: ?>
          <div class="adicional-name">Barras portaequipaje</div>
          <div class="adicional-desc">Ideales para llevar equipaje extra, bicicletas o equipo en tu aventura.</div>
        <?php endif; ?>
      </div>

      <!-- Sillita de bebé / Booster -->
      <div class="adicional-item">
        <div class="adicional-icon">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="5" r="2"/><path d="M12 8c-3 0-5 2-5 5v2h3l1 6h2l1-6h3v-2c0-3-2-5-5-5z"/></svg>
        </div>
        <?php if (qtranxf_getLanguage() == 'en'): ?>
          <div class="adicional-name">Baby seat / Booster</div>
          <div class="adicional-desc">Child safety seats available for babies and older children, ensuring a safe trip for the whole family.</div>
        <?php elseif (qtranxf_getLanguage() == 'pt'): ?>
          <div class="adicional-name">Cadeirinha / Booster</div>
          <div class="adicional-desc">Cadeiras de segurança disponíveis para bebês e crianças maiores, garantindo uma viagem segura para toda a família.</div>
        <?php else: ?>
          <div class="adicional-name">Sillita de bebé / Booster</div>
          <div class="adicional-desc">Sillas de seguridad disponibles para bebés y niños mayores, para que toda la familia viaje segura.</div>
        <?php endif; ?>
      </div>

      <!-- Pase a Chile -->
      <div class="adicional-item">
        <div class="adicional-icon">
          <svg viewBox="0 0 24 24"><path d="M3 3h7v4H3zM14 3h7v4h-7zM14 10h7v4h-7zM3 17h7v4H3zM14 17h7v4h-7zM3 10h7v4H3z" stroke="none" fill="#679938" opacity="0.15"/><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="12" y1="3" x2="12" y2="21"/><line x1="3" y1="12" x2="21" y2="12"/></svg>
        </div>
        <?php if (qtranxf_getLanguage() == 'en'): ?>
          <div class="adicional-name">Chile Border Pass</div>
          <div class="adicional-desc">Required permit to cross the border into Chile with our vehicles. Subject to prior authorization.</div>
        <?php elseif (qtranxf_getLanguage() == 'pt'): ?>
          <div class="adicional-name">Autorização para Chile</div>
          <div class="adicional-desc">Permissão necessária para cruzar a fronteira com o Chile com nossos veículos. Sujeito a autorização prévia.</div>
        <?php else: ?>
          <div class="adicional-name">Pase a Chile</div>
          <div class="adicional-desc">Permiso necesario para cruzar la frontera hacia Chile con nuestros vehículos. Sujeto a autorización previa.</div>
        <?php endif; ?>
      </div>

      <!-- Entrega fuera del radio céntrico -->
      <div class="adicional-item">
        <div class="adicional-icon">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="10" r="3"/><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><line x1="12" y1="22" x2="20" y2="22"/><line x1="16" y1="19" x2="20" y2="22"/><line x1="16" y1="25" x2="20" y2="22"/></svg>
        </div>
        <?php if (qtranxf_getLanguage() == 'en'): ?>
          <div class="adicional-name">Out-of-area delivery</div>
          <div class="adicional-desc">Vehicle delivery or return outside the city center or in other cities. Please enquire.</div>
        <?php elseif (qtranxf_getLanguage() == 'pt'): ?>
          <div class="adicional-name">Entrega fora da área central</div>
          <div class="adicional-desc">Entrega ou devolução do veículo fora do centro da cidade ou em outras cidades. Consulte.</div>
        <?php else: ?>
          <div class="adicional-name">Entrega fuera del radio céntrico</div>
          <div class="adicional-desc">Entrega y/o devolución del vehículo fuera del radio céntrico y en otras ciudades. Consultar.</div>
        <?php endif; ?>
      </div>

      <!-- Aeropuerto -->
      <div class="adicional-item">
        <div class="adicional-icon">
          <svg viewBox="0 0 24 24"><path d="M17.8 19.2L16 11l3.5-3.5C21 6 21 4 19.5 2.5S18 2 16.5 3.5L13 7 4.8 5.2 3.3 6.7l6.2 2.5L5 13H2l-1.5 1.5 3.5 2 2 3.5L7.5 18.5H7.5L9.5 17l2.5 6.2 1.5-1.5L11.5 13.5l3.5-3.5 2.5 6.2 1.8-1.5z"/></svg>
        </div>
        <?php if (qtranxf_getLanguage() == 'en'): ?>
          <div class="adicional-name">Airport service fee</div>
          <div class="adicional-desc">Additional charge for vehicle delivery or return at Bariloche Airport.</div>
        <?php elseif (qtranxf_getLanguage() == 'pt'): ?>
          <div class="adicional-name">Taxa de serviço no aeroporto</div>
          <div class="adicional-desc">Cobrança adicional pela entrega ou devolução do veículo no Aeroporto de Bariloche.</div>
        <?php else: ?>
          <div class="adicional-name">Tasa por servicio en Aeropuerto</div>
          <div class="adicional-desc">Cargo adicional por entrega y/o devolución del vehículo en el Aeropuerto de Bariloche.</div>
        <?php endif; ?>
      </div>

    </div>

    <p class="adicionales-nota">
      <?php if (qtranxf_getLanguage() == 'en'): ?>
        Prices may be updated at the time of vehicle delivery.
      <?php elseif (qtranxf_getLanguage() == 'pt'): ?>
        Os valores poderão ser atualizados no momento da entrega do veículo.
      <?php else: ?>
        Los valores podrán ser actualizados al momento de la entrega del vehículo.
      <?php endif; ?>
    </p>

  </div>
</div>

<?php get_template_part('book'); ?>
<?php get_footer(); ?>
