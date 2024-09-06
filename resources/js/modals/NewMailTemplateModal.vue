<template>
  <b-modal
    :id="modal_id"
    @hidden="emitNotification"
    size="md"
    scrollable
    no-fade
  >

      <template #modal-header>
              <span>New Mail Template</span>
      </template>
      <div class="container">

        <div class="form-group">
          <label for="create-code">Code</label>
          <ValidationProvider vid="code" name="Code" v-slot="{ errors }">
            <input
              id="create-code"
              type="text"
              class="form-control"
              v-model="mailTemplate.code"
              :class="{ 'is-invalid': errors.length }"
              required
            />
            <div class="invalid-feedback">{{ errors[0] }}</div>
          </ValidationProvider>
        </div>

        <div class="form-group">
            <label for="create-to">To</label>
            <ValidationProvider vid="to" name="To" v-slot="{ errors }">
              <input
                id="create-to"
                type="text"
                class="form-control"
                v-model="mailTemplate.to"
                :class="{ 'is-invalid': errors.length }"
              />
              <div class="invalid-feedback">{{ errors[0] }}</div>
            </ValidationProvider>
          </div>
  
          <div class="form-group">
            <label for="create-reply_to">Reply To</label>
            <ValidationProvider vid="reply_to" name="Reply To" v-slot="{ errors }">
              <input
                id="create-reply_to"
                type="text"
                class="form-control"
                v-model="mailTemplate.reply_to"
                :class="{ 'is-invalid': errors.length }"
              />
              <div class="invalid-feedback">{{ errors[0] }}</div>
            </ValidationProvider>
          </div>
  
          <div class="form-group">
            <label for="create-subject">Subject</label>
            <ValidationProvider vid="subject" name="Subject" v-slot="{ errors }">
              <input
                id="create-subject"
                type="text"
                class="form-control"
                v-model="mailTemplate.subject"
                :class="{ 'is-invalid': errors.length }"
                required
              />
              <div class="invalid-feedback">{{ errors[0] }}</div>
            </ValidationProvider>
          </div>
  
          <div class="form-group">
            <label for="create-html_template">HTML Template</label>
            <ValidationProvider vid="html_template" name="HTML Template" v-slot="{ errors }">
              <textarea
                id="create-html_template"
                class="form-control"
                v-model="mailTemplate.html_template"
                :class="{ 'is-invalid': errors.length }"
                rows="3"
                required
              ></textarea>
              <div class="invalid-feedback">{{ errors[0] }}</div>
            </ValidationProvider>
          </div>
  
          <div class="form-group">
            <label for="create-text_template">Text Template</label>
            <ValidationProvider vid="text_template" name="Text Template" v-slot="{ errors }">
              <textarea
                id="create-text_template"
                class="form-control"
                v-model="mailTemplate.text_template"
                :class="{ 'is-invalid': errors.length }"
                rows="3"
              ></textarea>
              <div class="invalid-feedback">{{ errors[0] }}</div>
            </ValidationProvider>
          </div>
        
      </div>

      <template #modal-footer>
          <b-button variant="secondary" class="float-right" @click="$bvModal.hide(modal_id);">
              Cancel
          </b-button>
          <b-button variant="primary" class="float-right" @click="createNewMailTemplate">
              Create
          </b-button>
      </template>
  </b-modal>
</template>

  <script>
  import { ValidationObserver, ValidationProvider } from 'vee-validate';
  import api from "../mixins/api.vue";
  import Modals from "../plugins/Modals";
  
  export default {
    mixins: [api],
    components: { ValidationObserver, ValidationProvider },
    data() {
      return {
        modal_id: 'new-mail-template-modal',
        template: undefined,
        mailTemplate: {
          code: '',
          to: '',
          reply_to: '',
          subject: '',
          html_template: '',
          text_template: ''
        },
      };
    },

    beforeMount() {
        Modals.EventBus.$on('show::modal::' + this.modal_id, (data) => {
            this.template = data['template'];

            this.mailTemplate = {
                to: '',
                reply_to: '',
                subject: '',
                html_template: '',
                text_template: ''
            };

            if (this.template) {
                this.mailTemplate.to = this.template.to;
                this.mailTemplate.reply_to = this.template.reply_to;
                this.mailTemplate.subject = this.template.subject;
                this.mailTemplate.html_template = this.template.html_template;
                this.mailTemplate.text_template = this.template.text_template;
            }

            this.$bvModal.show(this.modal_id);
        })
    },

    computed: {
        isCreatingNewTemplate() {
            return this.template === null || (this.template === undefined);
        }
    },

    methods: {
        createNewMailTemplate() {
            this.apiPostMailTemplate(this.mailTemplate)
                .then(({ data }) => {
                    this.$bvModal.hide(this.modal_id);
                    this.apiGetMailTemplate()
                        .then(({ data }) => {
                            this.mailTemplates = data.data;
                            Modals.EventBus.$emit('mailTemplateCreated', data.data);
                        })
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                });
      },
      emitNotification() {
        Modals.EventBus.$emit('hide::modal::' + this.modal_id, this.mailTemplate);
      }
    }
  };
  </script>
  