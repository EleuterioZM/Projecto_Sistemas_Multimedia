(customElements => {
  'strict';

  /**
   * Creates a custom element with the default spinner of the Joomla logo
   */
  class JoomlaCoreLoader extends HTMLElement {
    constructor() {
      super();
      const template = document.createElement('template');
      template.innerHTML = `<style>:host{z-index:10000;opacity:.8;align-items:center;width:100%;height:100%;display:flex;position:fixed;top:0;left:0;overflow:hidden}.box{width:345px;height:345px;margin:0 auto;position:relative}.box p{float:right;color:#999;margin:95px 0 0;font:1.25em/1em sans-serif}.box>span{animation:2s ease-in-out infinite jspinner}.box .red{animation-delay:-1.5s}.box .blue{animation-delay:-1s}.box .green{animation-delay:-.5s}.yellow{content:"";background:#f9a541;border-radius:90px;width:90px;height:90px;position:absolute;top:0;left:0}.yellow:before,.yellow:after{box-sizing:content-box;content:"";background:0 0;border:50px solid #f9a541;width:50px;position:absolute;top:0;left:0}.yellow:before{border-width:50px 50px 0;border-radius:75px 75px 0 0;height:35px;margin:60px 0 0 -30px}.yellow:after{border-width:0 0 0 50px;height:105px;margin:140px 0 0 -30px}.red{content:"";background:#f44321;border-radius:90px;width:90px;height:90px;position:absolute;top:0;left:0}.red:before,.red:after{box-sizing:content-box;content:"";background:0 0;border:50px solid #f44321;width:50px;position:absolute;top:0;left:0}.red:before{border-width:50px 50px 0;border-radius:75px 75px 0 0;height:35px;margin:60px 0 0 -30px}.red:after{border-width:0 0 0 50px;height:105px;margin:140px 0 0 -30px}.blue{content:"";background:#5091cd;border-radius:90px;width:90px;height:90px;position:absolute;top:0;left:0}.blue:before,.blue:after{box-sizing:content-box;content:"";background:0 0;border:50px solid #5091cd;width:50px;position:absolute;top:0;left:0}.blue:before{border-width:50px 50px 0;border-radius:75px 75px 0 0;height:35px;margin:60px 0 0 -30px}.blue:after{border-width:0 0 0 50px;height:105px;margin:140px 0 0 -30px}.green{content:"";background:#7ac143;border-radius:90px;width:90px;height:90px;position:absolute;top:0;left:0}.green:before,.green:after{box-sizing:content-box;content:"";background:0 0;border:50px solid #7ac143;width:50px;position:absolute;top:0;left:0}.green:before{border-width:50px 50px 0;border-radius:75px 75px 0 0;height:35px;margin:60px 0 0 -30px}.green:after{border-width:0 0 0 50px;height:105px;margin:140px 0 0 -30px}.yellow{margin:0 0 0 255px;transform:rotate(45deg)}.red{margin:255px 0 0 255px;transform:rotate(135deg)}.blue{margin:255px 0 0;transform:rotate(225deg)}.green{transform:rotate(315deg)}@keyframes jspinner{0%,40%,to{opacity:.3}20%{opacity:1}}@media (prefers-reduced-motion:reduce){.box>span{animation:none}}</style>
<div>
    <span class="yellow"></span>
    <span class="red"></span>
    <span class="blue"></span>
    <span class="green"></span>
    <p>&trade;</p>
</div>`;

      // Patch the shadow DOM
      if (window.ShadyCSS) {
        window.ShadyCSS.prepareTemplate(template, 'joomla-core-loader');
      }
      this.attachShadow({
        mode: 'open'
      });
      this.shadowRoot.appendChild(template.content.cloneNode(true));

      // Patch the shadow DOM
      if (window.ShadyCSS) {
        window.ShadyCSS.styleElement(this);
      }
    }
    connectedCallback() {
      this.style.backgroundColor = this.color;
      this.style.opacity = 0.8;
      this.shadowRoot.querySelector('div').classList.add('box');
    }
    static get observedAttributes() {
      return ['color'];
    }
    get color() {
      return this.getAttribute('color') || '#fff';
    }
    set color(value) {
      this.setAttribute('color', value);
    }
    attributeChangedCallback(attr, oldValue, newValue) {
      switch (attr) {
        case 'color':
          if (newValue && newValue !== oldValue) {
            this.style.backgroundColor = this.color;
          }
          break;
        // Do nothing
      }
    }
  }

  if (!customElements.get('joomla-core-loader')) {
    customElements.define('joomla-core-loader', JoomlaCoreLoader);
  }
})(customElements);
